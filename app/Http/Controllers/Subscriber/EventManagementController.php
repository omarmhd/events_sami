<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Event;
use App\Models\RegistrationForm;
use App\Services\RegistrationFormService;
use App\Services\SubscriptionService;
use App\Models\PublicEventRegistration;
use App\Models\EventAccessPass;
use App\Mail\TicketMail;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventManagementController extends Controller
{
    public function __construct(
        private RegistrationFormService $registrationFormService,
        private SubscriptionService $subscriptionService,
    ) {
    }

    /**
     * Resolve image feature flags for event uploads.
     *
     * Header image upload is always enabled regardless of subscription plan —
     * it is a core feature, not a gated one. The config still drives size/mime
     * constraints so those can be adjusted without touching controller code.
     */
    private function imageFeatureFlags(Company $company): array
    {
        return [
            'headerImageEnabled' => true,
            'headerImageCfg'     => config('features.event_header_image', []),
        ];
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $events = Event::query()
            ->when(!$user->isSystemAdmin(), function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->latest('id')
            ->paginate(10);

        // Get subscription quota data
        $company = $user->company;
        $subscription = $company?->activeSubscription();
        $quotaData = [
            'total' => $subscription?->annual_event_quota ?? 0,
            'used' => $subscription?->annual_events_used ?? 0,
            'remaining' => 0,
            'percentageUsed' => 0,
        ];

        if ($quotaData['total'] > 0) {
            $quotaData['remaining'] = max(0, $quotaData['total'] - $quotaData['used']);
            $quotaData['percentageUsed'] = round(($quotaData['used'] / $quotaData['total']) * 100, 1);
        }

        return view('subscriber.events.manage-index', [
            'events' => $events,
            'quota' => $quotaData,
            'planName' => $subscription?->plan->name ?? 'Basic Plan',
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $workspaceCompanyId = $this->workspaceCompanyId($user);

        if ($user->isSystemAdmin() && !$workspaceCompanyId) {
            return redirect()->route('system.dashboard')
                ->with('error', 'Create events from an organizer account workspace.');
        }

        if (!$workspaceCompanyId) {
            return redirect()->route('dashboard')
                ->with('error', 'Organizer company context is required to create events.');
        }

        $workspaceCompany = Company::find($workspaceCompanyId);
        $imageFlags = $workspaceCompany ? $this->imageFeatureFlags($workspaceCompany) : [];
        $regFormsEnabled = $workspaceCompany
            ? $this->subscriptionService->featureEnabled($workspaceCompany, 'registration_forms')
            : false;

        return view('subscriber.events.forms.event-form', array_merge([
            'event'                  => new Event(['company_id' => $workspaceCompanyId]),
            'mode'                   => 'create',
            'registrationForms'      => $this->registrationFormsForCompany($workspaceCompanyId),
            'registrationFormsEnabled' => $regFormsEnabled,
        ], $imageFlags));
    }

    public function store(Request $request)
    {
        $user    = $request->user();
        $company = $this->workspaceCompany($user);

        if (!$company) {
            return redirect()->route('system.dashboard')
                ->with('error', 'Organizer company context is required to create events.');
        }

        $imageFlags      = $this->imageFeatureFlags($company);
        $regFormsEnabled = $this->subscriptionService->featureEnabled($company, 'registration_forms');
        $data            = $this->validateEvent($request, $company->id, null, $imageFlags, $regFormsEnabled);

        // When registration_forms feature is disabled, public events have no custom form.
        // The registration page always shows guest_name + guest_email natively — no extra form needed.
        if ($data['event_type'] === 'public' && !$regFormsEnabled) {
            $data['registration_form_id'] = null;
        }

        // Store header image directly under public/uploads/event-images/headers/
        // so it is immediately accessible via a plain /uploads/... URL with no
        // symlink dependency (consistent with other upload areas in this project).
        $headerImageUrl = null;
        if ($imageFlags['headerImageEnabled'] && $request->hasFile('header_image')) {
            try {
                $headerImageUrl = $this->saveHeaderImage($request->file('header_image'));
            } catch (\Throwable $e) {
                Log::error('Header image upload failed (store)', [
                    'error'   => $e->getMessage(),
                    'user_id' => $request->user()->id ?? null,
                ]);
                return back()->withInput()
                    ->with('error', 'فشل رفع الصورة: ' . $e->getMessage());
            }
        }

        $check = $this->subscriptionService->canCreateEvent($company, $data['event_type']);

        if (!$check['allowed']) {
            return redirect()->route('billing.upgrade')
                ->with('error', $check['message']);
        }

        $event = Event::create([
            'organization_id' => $company->id,
            'company_id' => $company->id,
            'created_by' => $user->id,
            'event_slug' => $data['event_slug'],
            'name' => $data['title'],
            'title' => $data['title'],
            'event_type' => $data['event_type'],
            'experience_type' => $data['experience_type'],
            'registration_mode' => $data['event_type'] === 'public' ? 'public_link' : 'private_invites',
            'registration_form_id' => $data['registration_form_id'] ?? null,
            'date' => $data['date'],
            'from_time' => $data['from_time'],
            'to_time' => $data['to_time'],
            'start_datetime' => Carbon::parse($data['date'] . ' ' . $data['from_time']),
            'end_datetime' => Carbon::parse($data['date'] . ' ' . $data['to_time']),
            'location_name' => $data['location_name'],
            'google_map_url' => $data['google_map_url'],
            'description' => $data['description'],
            'description_en' => $data['description_en'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'schedule_items' => $data['schedule_items'],
            'header_image_path' => $headerImageUrl,
            'requires_manual_approval' => $request->boolean('requires_manual_approval'),
            'allow_reentry' => $request->boolean('allow_reentry'),
            // Subjects are auto-generated from event title at send time; not stored here.
            'invitation_email_body' => $data['invitation_email_body'] ?? null,
            'confirmation_email_body' => $data['confirmation_email_body'] ?? null,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        if ($event->status === 'published') {
            $this->subscriptionService->markEventConsumed($company);
        }

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Request $request, Event $event)
    {
        $this->authorizeEvent($request, $event);

        $company    = Company::find($event->company_id);
        $imageFlags = $company ? $this->imageFeatureFlags($company) : [];

        $regFormsEnabled = $company
            ? $this->subscriptionService->featureEnabled($company, 'registration_forms')
            : false;

        return view('subscriber.events.forms.event-form', array_merge([
            'event'                    => $event,
            'mode'                     => 'edit',
            'registrationForms'        => $this->registrationFormsForCompany($event->company_id),
            'registrationFormsEnabled' => $regFormsEnabled,
        ], $imageFlags));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($request, $event);

        $company    = Company::find($event->company_id);
        $imageFlags = $company ? $this->imageFeatureFlags($company) : [
            'headerImageEnabled' => true,
            'headerImageCfg'     => config('features.event_header_image', []),
        ];

        $regFormsEnabled = $company
            ? $this->subscriptionService->featureEnabled($company, 'registration_forms')
            : false;

        $data = $this->validateEvent($request, $event->company_id, $event->id, $imageFlags, $regFormsEnabled);

        // When registration_forms feature is disabled, clear any previously assigned form.
        // The registration page always shows guest_name + guest_email natively — no extra form needed.
        if ($data['event_type'] === 'public' && !$regFormsEnabled) {
            $data['registration_form_id'] = null;
        }

        $headerImageUrl = $event->header_image_path;

        // User clicked X to remove the image — delete file and clear the URL.
        if ($request->input('clear_header_image') === '1') {
            $this->deletePublicImage((string) $event->header_image_path);
            $headerImageUrl = null;
        }

        // New file uploaded — delete old file first, then save new one.
        if ($imageFlags['headerImageEnabled'] && $request->hasFile('header_image')) {
            try {
                $this->deletePublicImage((string) $event->header_image_path);
                $headerImageUrl = $this->saveHeaderImage($request->file('header_image'));
            } catch (\Throwable $e) {
                Log::error('Header image upload failed (update)', [
                    'error'   => $e->getMessage(),
                    'event'   => $event->id,
                    'user_id' => $request->user()->id ?? null,
                ]);
                return back()->withInput()
                    ->with('error', 'فشل رفع الصورة: ' . $e->getMessage());
            }
        }

        $wasRequiresManual = $event->requires_manual_approval;

        $event->update([
            'event_slug' => $data['event_slug'],
            'name' => $data['title'],
            'title' => $data['title'],
            'event_type' => $data['event_type'],
            'experience_type' => $data['experience_type'],
            'registration_mode' => $data['event_type'] === 'public' ? 'public_link' : 'private_invites',
            'registration_form_id' => $data['registration_form_id'] ?? null,
            'date' => $data['date'],
            'from_time' => $data['from_time'],
            'to_time' => $data['to_time'],
            'start_datetime' => Carbon::parse($data['date'] . ' ' . $data['from_time']),
            'end_datetime' => Carbon::parse($data['date'] . ' ' . $data['to_time']),
            'location_name' => $data['location_name'],
            'google_map_url' => $data['google_map_url'],
            'description' => $data['description'],
            'description_en' => $data['description_en'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'schedule_items' => $data['schedule_items'],
            'header_image_path' => $headerImageUrl,
            'requires_manual_approval' => $request->boolean('requires_manual_approval'),
            'allow_reentry' => $request->boolean('allow_reentry'),
            // Subjects are auto-generated from event title at send time; not stored here.
            'invitation_email_body' => $data['invitation_email_body'] ?? null,
            'confirmation_email_body' => $data['confirmation_email_body'] ?? null,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? ($event->published_at ?: now()) : null,
        ]);

        // If the organizer turned OFF manual approval for a public event,
        // accept all pending public registrations and send tickets immediately.
        if ($wasRequiresManual && !$event->requires_manual_approval && $event->event_type === 'public') {
            try {
                $pending = PublicEventRegistration::where('event_id', $event->id)
                    ->where('status', 'pending')
                    ->get();

                $qrService = app(QrCodeService::class);

                foreach ($pending as $registration) {
                    DB::transaction(function () use ($event, $registration, $qrService) {
                        $registration->update([
                            'status' => 'accepted',
                            'reviewed_by' => null,
                            'reviewed_at' => now(),
                        ]);

                        $pass = EventAccessPass::firstOrNew([
                            'event_id' => $event->id,
                            'passable_type' => PublicEventRegistration::class,
                            'passable_id' => $registration->id,
                            'type' => 'main',
                        ]);

                        if (!$pass->exists) {
                            $pass->token = (string) Str::uuid();
                        }

                        $pass->company_id = $event->company_id;
                        $pass->holder_name = $registration->guest_name;
                        $pass->holder_email = $registration->guest_email;
                        $pass->sent_at = now();
                        $pass->save();

                        $qr = $qrService->generateBase64($pass->token);
                        $tickets = [[ 'label' => 'Main', 'qr' => $qr ]];

                        $invitationProxy = (object) [
                            'invitee_name' => $registration->guest_name,
                            'invitee_email' => $registration->guest_email,
                            'invitee_position' => '',
                            'allowed_guests' => 0,
                            'selected_guests' => 0,
                        ];

                        Mail::to($registration->guest_email)->send(new TicketMail($invitationProxy, $tickets, $event));
                    });
                }
            } catch (\Throwable $e) {
                Log::error('Auto-accept pending registrations failed', ['event_id' => $event->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Request $request, Event $event)
    {
        $this->authorizeEvent($request, $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Validate event form data.
     *
     * Image rules are built dynamically from config/features.php so that
     * changing constraints (size, mimes, dimensions) only requires editing
     * that config file — no controller changes needed.
     *
     * @param  array  $imageFlags  Output of $this->imageFeatureFlags()
     */
    protected function validateEvent(Request $request, $companyId, $eventId = null, array $imageFlags = [], bool $regFormsEnabled = true)
    {
        $rawScheduleItems = json_decode((string) $request->input('schedule_items_json', '[]'), true);

        // ── Build header image validation rules from config ───────────────────
        // Footer image has been removed from the product; only header is relevant.
        $headerCfg     = $imageFlags['headerImageCfg'] ?? config('features.event_header_image', []);
        $headerEnabled = $imageFlags['headerImageEnabled'] ?? true;

        // When the feature is disabled, prohibit any upload attempt made via tampered forms.
        // Note: we intentionally skip Laravel's 'image' rule because it requires the GD extension
        // and can reject valid image files when GD is not fully configured (common on Windows dev).
        // The 'mimes:' rule (extension-based) and 'max:' are sufficient for safe validation here.
        $headerImageRules = $headerEnabled
            ? [
                'nullable',
                'file',
                'mimes:'  . ($headerCfg['mimes']  ?? 'jpg,jpeg,png,webp'),
                'max:'    . ($headerCfg['max_kb']  ?? 2048),
            ]
            : ['prohibited'];

        $data = $request->validate([
            'event_slug' => [
                'required',
                'alpha_dash',
                'min:3',
                'max:64',
                Rule::unique('events', 'event_slug')
                    ->where(function ($q) use ($companyId) {
                        return $q->where('company_id', $companyId);
                    })
                    ->ignore($eventId),
            ],
            'title' => ['required', 'string', 'max:190'],
            'event_type' => ['required', Rule::in(['private', 'public'])],
            'experience_type' => ['required', Rule::in(['conference', 'summit', 'workshop', 'training', 'networking', 'exhibition'])],
            'registration_form_id' => [
                // Only required when the feature is enabled; when disabled the
                // controller auto-assigns the default form after validation.
                Rule::requiredIf(fn () => $regFormsEnabled && $request->input('event_type') === 'public'),
                'nullable',
                'integer',
                Rule::exists('registration_forms', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'date' => ['required', 'date'],
            'from_time' => ['required'],
            'to_time' => ['required'],
            'location_name' => ['required', 'string', 'max:255'],
            'google_map_url' => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string'],
            'description_en' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'header_image' => $headerImageRules,
            'requires_manual_approval' => ['nullable', 'boolean'],
            'allow_reentry' => ['nullable', 'boolean'],
            // Email subjects are auto-generated; only body can be customised.
            'invitation_email_body' => ['nullable', 'string'],
            'confirmation_email_body' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        $data['schedule_items'] = $this->registrationFormService->normalizeScheduleItems($rawScheduleItems);

        return $data;
    }

    protected function authorizeEvent(Request $request, Event $event)
    {
        if ($request->user()->isSystemAdmin()) {
            return;
        }

        if ((int) $event->company_id !== (int) $this->workspaceCompanyId($request->user())) {
            abort(403);
        }
    }

    protected function registrationFormsForCompany(?int $companyId)
    {
        if (!$companyId) {
            return collect();
        }

        return RegistrationForm::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->where('name', '!=', '__default__')
            ->orderBy('name')
            ->get();
    }

    protected function workspaceCompanyId($user): ?int
    {
        return $user->company_id ?: $user->organization_id;
    }

    protected function workspaceCompany($user): ?Company
    {
        $companyId = $this->workspaceCompanyId($user);

        return $companyId ? Company::find($companyId) : null;
    }

    /**
     * Save an uploaded event header image to public/uploads/event-images/headers/
     * and return the public URL (/uploads/event-images/headers/filename.ext).
     *
     * Uses public_path() + move() — same pattern as logo/branding uploads —
     * so files are directly accessible without a storage symlink.
     */
    protected function saveHeaderImage(\Illuminate\Http\UploadedFile $file): string
    {
        $folder  = config('features.event_header_image.storage_folder', 'uploads/event-images/headers');
        $dir     = public_path($folder);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = uniqid('ev-header-', true) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return '/' . ltrim($folder, '/') . '/' . $filename;
    }

    /**
     * Delete an event header image from the public folder.
     * Safely handles empty URLs, absolute URLs, and /uploads/... paths.
     */
    protected function deletePublicImage(string $url): void
    {
        if ($url === '') {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';

        // Only delete files we manage under /uploads/
        if (!str_starts_with($path, '/uploads/')) {
            return;
        }

        $absolute = public_path(ltrim($path, '/'));
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }
}




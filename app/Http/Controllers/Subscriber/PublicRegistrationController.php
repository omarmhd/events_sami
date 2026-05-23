<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Mail\TicketMail;
use App\Mail\DynamicTemplateMail;
use App\Models\Event;
use App\Models\EventAccessPass;
use App\Models\EmailTemplate;
use App\Models\PublicEventRegistration;
use App\Services\EmailTemplateService;
use App\Services\RegistrationFormService;
use App\Services\QrCodeService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PublicRegistrationController extends Controller
{
    public function __construct(private RegistrationFormService $registrationFormService)
    {
    }

    public function publicForm($eventSlug)
    {
        $event = Event::query()
            ->where('event_slug', $eventSlug)
            ->where('event_type', 'public')
            ->with('registrationForm')
            ->firstOrFail();

        return view('home.events.event-registration-page', [
            'event' => $event,
            'dynamicFields' => $this->registrationFormService->normalizeFields(optional($event->registrationForm)->fields),
        ]);
    }

    public function submitPublicForm(Request $request, $eventSlug, SubscriptionService $subscriptionService, EmailTemplateService $templateService)
    {
        $event = Event::query()
            ->where('event_slug', $eventSlug)
            ->where('event_type', 'public')
            ->with('registrationForm')
            ->firstOrFail();

        // Validate fixed fields + dynamic form fields first so we have the email.
        $data = $request->validate(array_merge([
            'guest_name'  => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
        ], $this->registrationFormService->buildValidationRules(optional($event->registrationForm)->fields)));

        // ── Duplicate registration guard ──────────────────────────────────────
        // If this email is already registered for the event, show a clear
        // message and do NOT send another pass.
        $existing = PublicEventRegistration::where('event_id', $event->id)
            ->where('guest_email', $data['guest_email'])
            ->first();

        if ($existing) {
            $statusMessage = match ($existing->status) {
                'accepted' => 'لقد سجّلت في هذه الفعالية مسبقاً. تم إرسال بطاقة دخولك إلى بريدك الإلكتروني.',
                'pending'  => 'طلب تسجيلك قيد المراجعة. سنُخطرك بالنتيجة قريباً.',
                'rejected' => 'تعذّر قبول طلب تسجيلك في هذه الفعالية.',
                default    => 'بريدك الإلكتروني مسجّل مسبقاً في هذه الفعالية.',
            };

            return back()->with('info', $statusMessage);
        }

        // ── Subscription quota check ──────────────────────────────────────────
        $company = $event->company;
        if ($company) {
            $limitCheck = $subscriptionService->canAddInvitees($company, $event, 1);
            if (!$limitCheck['allowed']) {
                return back()->withErrors(['guest_email' => $limitCheck['message']])->withInput();
            }
        }

        $formPayload = $this->registrationFormService->normalizePayload(
            (array) $request->input('form_payload', []),
            optional($event->registrationForm)->fields
        );

        $requiresManualApproval = (bool) $event->requires_manual_approval;

        $registration = PublicEventRegistration::create([
            'event_id'             => $event->id,
            'registration_form_id' => $event->registration_form_id,
            'organization_id'      => $event->organization_id ?: $event->company_id,
            'company_id'           => $event->company_id,
            'guest_name'           => $data['guest_name'],
            'guest_email'          => $data['guest_email'],
            'guest_phone'          => null,
            'guest_position'       => null,
            'guest_nationality'    => null,
            'form_payload'         => $formPayload,
            'status'               => $requiresManualApproval ? 'pending' : 'accepted',
            'approval_token'       => (string) Str::uuid(),
        ]);

        if ($requiresManualApproval) {
            return back()->with('success', 'تم إرسال طلب تسجيلك بنجاح. سيتم مراجعته والردّ عليك قريباً.');
        }

        // Issue pass and send ticket email using the same TicketMail used for
        // private invitations — it embeds QR and renders correctly in all clients.
        $this->issuePassAndNotify($event, $registration, $templateService);

        return back()->with('success', 'تم تسجيلك بنجاح! تم إرسال بطاقة الدخول إلى بريدك الإلكتروني.');
    }

    public function reviewQueue(Request $request, Event $event)
    {
        $this->authorizeEvent($request, $event);

        $event->load('registrationForm');

        $search = trim((string) $request->input('searchInput', $request->input('search', '')));
        $statusFilter = (string) $request->input('status', 'all');
        $allowedStatuses = ['all', 'pending', 'accepted', 'rejected'];
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $baseQuery = PublicEventRegistration::query()
            ->where('event_id', $event->id);

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhere('guest_phone', 'like', "%{$search}%")
                    ->orWhere('guest_position', 'like', "%{$search}%")
                    ->orWhere('guest_nationality', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $rowsQuery = (clone $baseQuery)->latest('id');
        if ($statusFilter !== 'all') {
            $rowsQuery->where('status', $statusFilter);
        }

        $rows = $rowsQuery->paginate(25);
        $rows->appends($request->query());

        $statusStats = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalRegistrations = (int) $statusStats->sum();

        return view('subscriber.events.registrations-review', [
            'event' => $event,
            'rows' => $rows,
            'dynamicFields' => $this->registrationFormService->normalizeFields(optional($event->registrationForm)->fields),
            'search' => $search,
            'statusFilter' => $statusFilter,
            'statusStats' => $statusStats,
            'totalRegistrations' => $totalRegistrations,
        ]);
    }

    public function reviewDecision(Request $request, Event $event, PublicEventRegistration $registration, EmailTemplateService $templateService)
    {
        $this->authorizeEvent($request, $event);

        if ((int) $registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $data = $request->validate([
            'decision' => ['required', 'in:accepted,rejected'],
        ]);

        $registration->update([
            'status' => $data['decision'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        if ($data['decision'] === 'accepted') {
            // issuePassAndNotify handles mail errors internally and logs them.
            $this->issuePassAndNotify($event, $registration, $templateService);
        }

        if ($data['decision'] === 'rejected') {
            try {
                if ($event->company) {
                    $compiled = $templateService->compile(
                        $event->company,
                        $event,
                        EmailTemplate::TYPE_PUBLIC_REJECTED,
                        [
                            'guest_name' => $registration->guest_name,
                            'guest_email' => $registration->guest_email,
                            'rsvp_status' => 'declined',
                        ]
                    );

                    Mail::to($registration->guest_email)
                        ->send(new DynamicTemplateMail($compiled));
                } else {
                    Mail::raw(
                        'Thank you for your interest. Unfortunately, your registration was not approved.',
                        function ($message) use ($registration) {
                            $message->to($registration->guest_email)->subject('Event registration update');
                        }
                    );
                }
            } catch (Throwable $e) {
                Log::error('Failed to send rejection email', [
                    'registration_id' => $registration->id,
                    'email'           => $registration->guest_email,
                    'error'           => $e->getMessage(),
                ]);
                // Decision is already saved — do not fail the review because of a mail error.
                return back()->with('warning', 'تم رفض التسجيل لكن فشل إرسال البريد الإلكتروني: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Registration reviewed successfully.');
    }

    public function destroy(Request $request, Event $event, PublicEventRegistration $registration)
    {
        $this->authorizeEvent($request, $event);

        if ((int) $registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $this->authorize('delete', $registration);

        DB::transaction(function () use ($registration) {
            $registration->accessPasses()->delete();
            $registration->delete();
        });

        return back()->with('success', 'تم حذف التسجيل بنجاح.');
    }

    public function showPass($token, QrCodeService $qrCodeService)
    {
        $pass = EventAccessPass::query()->where('token', $token)->firstOrFail();

        return view('home.events.event-access-pass-page', [
            'pass' => $pass,
            'qr' => $qrCodeService->generateBase64($pass->token),
        ]);
    }

    protected function authorizeEvent(Request $request, Event $event)
    {
        if ($request->user()->isSystemAdmin()) {
            return;
        }

        if ((int) $request->user()->company_id !== (int) $event->company_id) {
            abort(403);
        }
    }

    /**
     * Issue an EventAccessPass and send the ticket email via TicketMail —
     * the same Mailable used for private invitations, which correctly embeds
     * the QR code and renders consistently across all email clients.
     *
     * On approval after manual review, reviewDecision() also calls this method.
     */
    protected function issuePassAndNotify(Event $event, PublicEventRegistration $registration, EmailTemplateService $templateService): void
    {
        // ── Persist the access pass ────────────────────────────────────────────
        $pass = EventAccessPass::firstOrNew([
            'event_id'      => $event->id,
            'passable_type' => PublicEventRegistration::class,
            'passable_id'   => $registration->id,
            'type'          => 'main',
        ]);

        if (!$pass->exists) {
            $pass->token = (string) Str::uuid();
        }

        $pass->company_id   = $event->company_id;
        $pass->holder_name  = $registration->guest_name;
        $pass->holder_email = $registration->guest_email;
        $pass->sent_at      = now();
        $pass->save();

        // ── Generate QR for the pass token ────────────────────────────────────
        $qrService = app(QrCodeService::class);
        $qrBase64  = $qrService->generateBase64($pass->token);

        // ── Build ticket array in the same shape TicketMail expects ───────────
        // TicketMail / emails.tickets.blade.php iterates $tickets and renders
        // each entry with label + qr. We pass a single "Main" pass.
        $tickets = [
            [
                'label' => 'Main',
                'qr'    => $qrBase64,
            ],
        ];

        // ── Build a lightweight invitation-like object for TicketMail ─────────
        // TicketMail was designed for EventInvitation models but only reads a
        // handful of display properties. We use a plain stdClass so we don't
        // couple this controller to the invitation domain.
        $invitationProxy = (object) [
            'invitee_name'     => $registration->guest_name,
            'invitee_email'    => $registration->guest_email,
            'invitee_position' => '',
            'allowed_guests'   => 0,
            'selected_guests'  => 0,
        ];

        try {
            Mail::to($registration->guest_email)
                ->send(new TicketMail($invitationProxy, $tickets, $event));
        } catch (Throwable $e) {
            // Pass is already saved — log the mail failure but do not throw.
            // The pass URL remains accessible via the EventAccessPass record.
            Log::error('Failed to send public registration ticket email', [
                'registration_id' => $registration->id,
                'email'           => $registration->guest_email,
                'pass_token'      => $pass->token,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}




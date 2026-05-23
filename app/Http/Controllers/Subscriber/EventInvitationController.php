<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Imports\InvitationsImport;
use App\Jobs\SendInvitationEmail;
use App\Mail\InvitationSent;
use App\Mail\TicketMail;
use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\InvitationQr;
use App\Services\PublicUrlService;
use App\Services\QrCodeService;
use App\Services\SubscriptionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Throwable;

class EventInvitationController extends Controller
{
    private function resolveEventForInvitation(EventInvitation $invitation): ?Event
    {
        if ($invitation->relationLoaded('event') && $invitation->event) {
            return $invitation->event;
        }

        if ($invitation->event) {
            return $invitation->event;
        }

        if ($invitation->company_id) {
            $companyEvent = Event::query()
                ->where('company_id', $invitation->company_id)
                ->latest('id')
                ->first();

            if ($companyEvent) {
                return $companyEvent;
            }
        }

        return Event::query()->latest('id')->first();
    }

    public function index(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();
        $query = EventInvitation::query()->with('event');

        if ($user && !$user->isSystemAdmin() && $user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        $search = trim((string) $request->input('searchInput', $request->input('search', '')));

        if ($search !== '') {

            $query->where(function ($q) use ($search) {
                $q->where('invitee_name', 'like', "%{$search}%")
                    ->orWhere('invitee_email', 'like', "%{$search}%")
                    ->orWhere('invitee_phone', 'like', "%{$search}%")
                    ->orWhere('invitee_position', 'like', "%{$search}%")
                    ->orWhere('invitee_nationality', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', (int) $request->input('event_id'));
        }

        $activeStatus = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'accepted', 'declined', 'rejected', 'maybe', 'sent'];
        if (!in_array($activeStatus, $allowedStatuses, true)) {
            $activeStatus = 'all';
        }

        $statusStats = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        if ($activeStatus === 'declined') {
            $query->whereIn('status', ['declined', 'rejected']);
        } elseif ($activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        }

        $rows = $query->latest()->paginate(10);
        $rows->appends($request->query());

        $events = Event::query()
            ->when($user && !$user->isSystemAdmin(), function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->orderByDesc('id')
            ->get();

        $event = $request->filled('event_id')
            ? $events->firstWhere('id', (int) $request->input('event_id'))
            : $events->first();

        $canCsvImport = false;
        $canBulkResend = false;

        if ($user && $user->isSystemAdmin()) {
            $canCsvImport = true;
            $canBulkResend = true;
        } elseif ($user && $user->company) {
            $canCsvImport = $subscriptionService->featureEnabled($user->company, 'csv_import');
            $canBulkResend = $subscriptionService->featureEnabled($user->company, 'bulk_resend');
        }

        $totalInvitations = (int) $statusStats->sum();
        $declinedCount = (int) (($statusStats->get('declined', 0)) + ($statusStats->get('rejected', 0)));

        return view('subscriber.invitations.list', [
            'invitations' => $rows,
            'statusStats' => $statusStats,
            'activeStatus' => $activeStatus,
            'totalInvitations' => $totalInvitations,
            'declinedCount' => $declinedCount,
            'event' => $event,
            'events' => $events,
            'selectedEventId' => $event?->id,
            'search' => $search,
            'canCsvImport' => $canCsvImport,
            'canBulkResend' => $canBulkResend,
        ]);
    }

    public function create(Request $request)
    {
        $events = Event::query()
            ->when($request->user() && !$request->user()->isSystemAdmin(), function ($q) use ($request) {
                $q->where('company_id', $request->user()->company_id);
            })
            ->orderByDesc('id')
            ->get();

        if ($events->isEmpty()) {
            return redirect()->route('events.create')
                ->with('error', __('invitations.create.messages.create_event_first'));
        }

        return view('subscriber.invitations.create', compact('events'));
    }

    public function store(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();

        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'invitee_name' => ['required', 'string', 'max:255'],
            'invitee_email' => ['required', 'email', 'max:255'],
            'invitee_phone' => ['nullable', 'string', 'max:40'],
            'invitee_position' => ['nullable', 'string', 'max:255'],
            'invitee_nationality' => ['nullable', 'string', 'max:255'],
            'allowed_guests' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $this->authorizeEventForUser($event, $user);
        $company = $event->company ?: $user->company;

        $alreadyExists = EventInvitation::query()
            ->where('event_id', $event->id)
            ->where('invitee_email', $request->invitee_email)
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'invitee_email' => __('invitations.create.messages.email_already_invited'),
            ])->withInput();
        }

        if ($company && !$user->isSystemAdmin()) {
            $limitCheck = $subscriptionService->canAddInvitees($company, $event, 1);
            if (!$limitCheck['allowed']) {
                return redirect()->route('billing.upgrade')->with('error', $limitCheck['message']);
            }
        }

        $invitation = EventInvitation::create([
            'company_id' => $company ? $company->id : null,
            'event_id' => $event->id,
            'flow_type' => 'private',
            'source' => 'manual',
            'invitee_name' => $request->invitee_name,
            'invitee_email' => $request->invitee_email,
            'invitee_phone' => $request->invitee_phone,
            'invitee_position' => $request->invitee_position,
            'invitee_nationality' => $request->invitee_nationality,
            'allowed_guests' => $request->allowed_guests,
            'invitation_token' => (string) Str::uuid(),
            'last_sent_at' => now(),
        ]);

        // Use PublicUrlService so the link uses the tenant subdomain when the
        // company's plan has the `custom_subdomain` feature enabled.
        $invitationLink = $company
            ? app(PublicUrlService::class)->rsvpUrl($company, $invitation->invitation_token)
            : route('rsvp.show', $invitation->invitation_token);

        if ($invitation->invitee_email) {
            try {
                Mail::to($invitation->invitee_email)
                    ->send(new InvitationSent($invitation, $invitationLink, $event));
            } catch (Throwable $e) {
                Log::error('Failed to send invitation email (store)', [
                    'invitation_id' => $invitation->id,
                    'email'         => $invitation->invitee_email,
                    'error'         => $e->getMessage(),
                ]);
                // Invitation record is already saved; notify user that email failed
                // but do not roll back — they can resend from the invitations list.
                return redirect()->route('events.invitations.index', $event)
                    ->with('warning', 'تم حفظ الدعوة لكن فشل إرسال البريد الإلكتروني: ' . $e->getMessage());
            }
        }

        return redirect()->route('events.invitations.index', $event)
            ->with('success', __('invitations.create.messages.invitation_sent'));
    }

    public function edit(Request $request, $id)
    {
        $eventInvitation = EventInvitation::findOrFail($id);
        $this->authorizeInvitationForUser($eventInvitation, $request->user());

        $events = Event::query()
            ->when(!$request->user()->isSystemAdmin(), function ($q) use ($request) {
                $q->where('company_id', $request->user()->company_id);
            })
            ->orderByDesc('id')
            ->get();

        return view('subscriber.invitations.edit', [
            'row' => $eventInvitation,
            'events' => $events,
        ]);
    }

    public function update(Request $request, $id)
    {
        $invitation = EventInvitation::findOrFail($id);
        $this->authorizeInvitationForUser($invitation, $request->user());

        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'invitee_name' => ['required', 'string', 'max:255'],
            'invitee_email' => ['required', 'email', 'max:255'],
            'invitee_phone' => ['nullable', 'string', 'max:40'],
            'invitee_position' => ['nullable', 'string', 'max:255'],
            'invitee_nationality' => ['nullable', 'string', 'max:255'],
            'allowed_guests' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $duplicate = EventInvitation::query()
            ->where('event_id', $request->event_id)
            ->where('invitee_email', $request->invitee_email)
            ->where('id', '<>', $invitation->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'invitee_email' => 'This email is already invited for the selected event.',
            ])->withInput();
        }

        $invitation->update([
            'event_id' => $request->event_id,
            'invitee_name' => $request->invitee_name,
            'invitee_email' => $request->invitee_email,
            'invitee_phone' => $request->invitee_phone,
            'invitee_position' => $request->invitee_position,
            'invitee_nationality' => $request->invitee_nationality,
            'allowed_guests' => $request->allowed_guests,
        ]);

        return redirect()->route('invitations.index')
            ->with('success', 'Invitation updated successfully.');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:event_invitations,id',
        ]);

        $invitation = EventInvitation::findOrFail($request->id);
        $this->authorizeInvitationForUser($invitation, $request->user());

        $invitation->update([
            'status' => 'pending',
            'responded_at' => null,
            'selected_guests' => 0,
            'invitation_token' => (string) Str::uuid(),
            'source' => 'resend',
            'last_sent_at' => now(),
        ]);

        $invitation->accessPasses()->delete();

        $event = $this->resolveEventForInvitation($invitation);

        // Resolve the correct company for this invitation so PublicUrlService
        // can decide whether to use a subdomain URL.
        $resendCompany = $invitation->company ?? ($event ? $event->company : null);
        $invitationLink = $resendCompany
            ? app(PublicUrlService::class)->rsvpUrl($resendCompany, $invitation->invitation_token)
            : route('rsvp.show', $invitation->invitation_token);

        InvitationQr::where('event_invitation_id', $invitation->id)->delete();

        if ($invitation->invitee_email && $event) {
            try {
                Mail::to($invitation->invitee_email)
                    ->send(new InvitationSent($invitation, $invitationLink, $event));
            } catch (Throwable $e) {
                Log::error('Failed to send invitation email (resend)', [
                    'invitation_id' => $invitation->id,
                    'email'         => $invitation->invitee_email,
                    'error'         => $e->getMessage(),
                ]);
                return back()->with('warning', 'فشل إرسال البريد الإلكتروني: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Invitation resent successfully.');
    }

    public function resendAll(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && (!$user->company || !$subscriptionService->featureEnabled($user->company, 'bulk_resend'))) {
            return redirect()->route('feature.unavailable', ['feature' => 'bulk_resend']);
        }

        $eventId = (int) $request->input('event_id');

        $query = EventInvitation::query();
        if (!$user->isSystemAdmin()) {
            $query->where('company_id', $user->company_id);
        }
        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $invitations = $query->get();

        foreach ($invitations as $invitation) {
            /** @var \App\Models\EventInvitation $invitation */
            $invitation->update([
                'status' => 'pending',
                'responded_at' => null,
                'selected_guests' => 0,
                'invitation_token' => (string) Str::uuid(),
                'source' => 'resend',
                'last_sent_at' => now(),
            ]);

            $invitation->accessPasses()->delete();

            InvitationQr::where('event_invitation_id', $invitation->id)->delete();

            $event = $invitation->event;
            if ($invitation->invitee_email && $event) {
                $bulkCompany = $invitation->company ?? $event->company ?? null;
                $resendLink  = $bulkCompany
                    ? app(PublicUrlService::class)->rsvpUrl($bulkCompany, $invitation->invitation_token)
                    : route('rsvp.show', $invitation->invitation_token);
                Mail::to($invitation->invitee_email)
                    ->send(new InvitationSent($invitation, $resendLink, $event));
            }
        }

        return back()->with('success', 'All invitations were resent successfully.');
    }

    public function bulkResendSelected(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && (!$user->company || !$subscriptionService->featureEnabled($user->company, 'bulk_resend'))) {
            return response()->json(['success' => false, 'message' => 'هذه الميزة غير متاحة في خطتك الحالية.'], 403);
        }

        $validated = $request->validate([
            'selected_ids'   => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:event_invitations,id'],
        ]);

        $query = EventInvitation::query()->whereIn('id', $validated['selected_ids']);
        if (!$user->isSystemAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $invitations = $query->with('event')->get();

        if ($invitations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'لم يتم العثور على دعوات محددة.'], 404);
        }

        $sentCount = 0;
        foreach ($invitations as $invitation) {
            /** @var \App\Models\EventInvitation $invitation */
            $invitation->update([
                'status'           => 'pending',
                'responded_at'     => null,
                'selected_guests'  => 0,
                'invitation_token' => (string) Str::uuid(),
                'source'           => 'resend',
                'last_sent_at'     => now(),
            ]);

            $invitation->accessPasses()->delete();

            InvitationQr::where('event_invitation_id', $invitation->id)->delete();

            if ($invitation->invitee_email && $invitation->event) {
                $selectedCompany = $invitation->company ?? $invitation->event->company ?? null;
                $selectedLink    = $selectedCompany
                    ? app(PublicUrlService::class)->rsvpUrl($selectedCompany, $invitation->invitation_token)
                    : route('rsvp.show', $invitation->invitation_token);
                Mail::to($invitation->invitee_email)
                    ->send(new InvitationSent($invitation, $selectedLink, $invitation->event));
                $sentCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم إعادة إرسال {$sentCount} دعوة بنجاح.",
        ]);
    }

    public function bulkDestroySelected(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:event_invitations,id'],
        ]);

        $query = EventInvitation::query()->whereIn('id', $validated['selected_ids']);
        if (!$user->isSystemAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $invitations = $query->get();

        if ($invitations->isEmpty()) {
            return back()->with('error', 'No invitations were selected.');
        }

        foreach ($invitations as $invitation) {
            InvitationQr::where('event_invitation_id', $invitation->id)->delete();
            $invitation->delete();
        }

        return back()->with('success', $invitations->count() . ' invitations were deleted successfully.');
    }

    public function importCsv(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && (!$user->company || !$subscriptionService->featureEnabled($user->company, 'csv_import'))) {
            return redirect()->route('feature.unavailable', ['feature' => 'csv_import']);
        }

        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $this->authorizeEventForUser($event, $user);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($file === false) {
            return back()->with('error', 'Unable to read CSV file.');
        }

        fgetcsv($file);
        $rows = [];

        while (($line = fgetcsv($file)) !== false) {
            if (count($line) < 2) {
                continue;
            }

            $rows[] = [
                'invitee_name' => trim($line[0]),
                'invitee_email' => trim($line[1]),
                'invitee_position' => isset($line[2]) ? trim($line[2]) : null,
                'invitee_nationality' => isset($line[3]) ? trim($line[3]) : null,
                'allowed_guests' => isset($line[4]) && is_numeric($line[4]) ? (int) $line[4] : 0,
            ];
        }

        fclose($file);

        $company = $event->company ?: $user->company;

        if (!$user->isSystemAdmin() && $company) {
            $limitCheck = $subscriptionService->canAddInvitees($company, $event, count($rows));
            if (!$limitCheck['allowed']) {
                return redirect()->route('billing.upgrade')->with('error', $limitCheck['message']);
            }
        }

        $imported     = 0;
        $emailsFailed = 0;

        foreach ($rows as $row) {
            if (!$row['invitee_name'] || !$row['invitee_email']) {
                continue;
            }

            $exists = EventInvitation::query()
                ->where('event_id', $event->id)
                ->where('invitee_email', $row['invitee_email'])
                ->exists();

            if ($exists) {
                continue;
            }

            $invitation = EventInvitation::create([
                'company_id'          => $company ? $company->id : null,
                'event_id'            => $event->id,
                'flow_type'           => 'private',
                'source'              => 'csv_import',
                'invitee_name'        => $row['invitee_name'],
                'invitee_email'       => $row['invitee_email'],
                'invitee_position'    => $row['invitee_position'],
                'invitee_nationality' => $row['invitee_nationality'],
                'allowed_guests'      => min(10, max(0, (int) $row['allowed_guests'])),
                'invitation_token'    => (string) Str::uuid(),
                'last_sent_at'        => now(),
            ]);

            $csvLink = $company
                ? app(PublicUrlService::class)->rsvpUrl($company, $invitation->invitation_token)
                : route('rsvp.show', $invitation->invitation_token);

            try {
                Mail::to($invitation->invitee_email)
                    ->send(new InvitationSent($invitation, $csvLink, $event));
            } catch (Throwable $e) {
                Log::error('Failed to send invitation email (csv_import)', [
                    'invitation_id' => $invitation->id,
                    'email'         => $invitation->invitee_email,
                    'error'         => $e->getMessage(),
                ]);
                $emailsFailed++;
            }

            $imported++;
        }

        $message = "CSV import completed. {$imported} invitation(s) created.";
        if ($emailsFailed > 0) {
            $message .= " {$emailsFailed} email(s) failed to send — check storage/logs/laravel.log for details.";
            return back()->with('warning', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Import invitations from an Excel (.xlsx) or CSV file.
     * Uses the same subscription-gated path as importCsv but dispatches
     * email sends as queued jobs instead of blocking the request.
     */
    public function importExcel(Request $request, SubscriptionService $subscriptionService)
    {
        $user = $request->user();

        if (!$user->isSystemAdmin() && (!$user->company || !$subscriptionService->featureEnabled($user->company, 'csv_import'))) {
            return redirect()->route('feature.unavailable', ['feature' => 'csv_import']);
        }

        $request->validate([
            'event_id'    => ['required', 'exists:events,id'],
            'excel_file'  => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $this->authorizeEventForUser($event, $user);

        // Parse the uploaded file using the reusable InvitationsImport class.
        $import = new InvitationsImport();
        Excel::import($import, $request->file('excel_file'));
        $rows = $import->getRows();

        if (empty($rows)) {
            return back()->with('error', 'لم يتم العثور على بيانات صالحة في الملف. تأكد من استخدام نموذج الاستيراد الصحيح.');
        }

        $company = $event->company ?: $user->company;

        if (!$user->isSystemAdmin() && $company) {
            $limitCheck = $subscriptionService->canAddInvitees($company, $event, count($rows));
            if (!$limitCheck['allowed']) {
                return redirect()->route('billing.upgrade')->with('error', $limitCheck['message']);
            }
        }

        $imported     = 0;
        $skipped      = 0;
        $emailsFailed = 0;

        foreach ($rows as $row) {
            // Skip duplicates — one invitation per email per event.
            $exists = EventInvitation::query()
                ->where('event_id', $event->id)
                ->where('invitee_email', $row['invitee_email'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $invitation = EventInvitation::create([
                'company_id'          => $company ? $company->id : null,
                'event_id'            => $event->id,
                'flow_type'           => 'private',
                'source'              => 'excel_import',
                'invitee_name'        => $row['invitee_name'],
                'invitee_email'       => $row['invitee_email'],
                'invitee_position'    => $row['invitee_position'] ?: null,
                'invitee_nationality' => $row['invitee_nationality'] ?: null,
                'allowed_guests'      => min(10, max(0, (int) $row['allowed_guests'])),
                'invitation_token'    => (string) Str::uuid(),
                'last_sent_at'        => now(),
            ]);

            // Use dispatchSync() so the job runs immediately inside this request and
            // any email errors are caught and reported rather than silently queued.
            // When the queue driver is later changed to redis/database, switch back
            // to dispatch() so large imports don't block the HTTP response.
            try {
                SendInvitationEmail::dispatchSync($invitation);
            } catch (Throwable $e) {
                Log::error('Failed to send invitation email (excel_import)', [
                    'invitation_id' => $invitation->id,
                    'email'         => $invitation->invitee_email,
                    'error'         => $e->getMessage(),
                ]);
                $emailsFailed++;
            }

            $imported++;
        }

        $message = "تم الاستيراد بنجاح: {$imported} دعوة.";
        if ($skipped > 0) {
            $message .= " تم تخطي {$skipped} سجل (مكرر).";
        }
        if ($emailsFailed > 0) {
            $message .= " فشل إرسال {$emailsFailed} بريد إلكتروني — راجع storage/logs/laravel.log.";
            return back()->with('warning', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Download a ready-made Excel template for the invitation import.
     * The template includes a header row with Arabic + English column labels,
     * sample rows, column widths, and a validation note.
     */
    public function downloadExcelTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Invitations Template');

        // ── Header row ──────────────────────────────────────────────────────────
        $headers = [
            'A1' => 'name',
            'B1' => 'email',
            'C1' => 'position',
            'D1' => 'nationality',
            'E1' => 'allowed_guests',
        ];

        // Arabic sub-headers in row 2 for user guidance (import reads row 1 only)
        $arabicHints = [
            'A2' => 'الاسم الكامل',
            'B2' => 'البريد الإلكتروني',
            'C2' => 'المسمى الوظيفي',
            'D2' => 'الجنسية',
            'E2' => 'عدد المرافقين',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        foreach ($arabicHints as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row — teal background, white bold text, centred.
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F8F83']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB0D8D4']]],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Arabic hint row — light teal background.
        $hintStyle = [
            'font'      => ['italic' => true, 'color' => ['argb' => 'FF0F6B62'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F7F5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A2:E2')->applyFromArray($hintStyle);

        // ── Sample data rows ────────────────────────────────────────────────────
        $samples = [
            ['أحمد محمد السالم', 'ahmed.salem@example.com', 'مدير تسويق', 'سعودي', 2],
            ['Sara Abdullah',     'sara@example.com',         'Engineer',    'Emirati', 1],
            ['محمد العمري',       'omar@example.com',         'مستشار',     'كويتي',   0],
        ];
        $row = 3;
        foreach ($samples as $sample) {
            $sheet->setCellValue("A{$row}", $sample[0]);
            $sheet->setCellValue("B{$row}", $sample[1]);
            $sheet->setCellValue("C{$row}", $sample[2]);
            $sheet->setCellValue("D{$row}", $sample[3]);
            $sheet->setCellValue("E{$row}", $sample[4]);
            $row++;
        }

        // Style sample rows — alternating light backgrounds.
        $sheet->getStyle('A3:E5')->applyFromArray([
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['argb' => 'FFD1E8E4']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // ── Column widths ───────────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(16);

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Freeze the first two rows so headers stay visible while scrolling.
        $sheet->freezePane('A3');

        // ── Note row ────────────────────────────────────────────────────────────
        $noteRow = $row + 1;
        $sheet->setCellValue("A{$noteRow}", '* لا تحذف أو تعدّل صف العناوين (الصف الأول). البيانات تبدأ من الصف الثالث فما فوق.');
        $sheet->getStyle("A{$noteRow}:E{$noteRow}")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['argb' => 'FF9CA3AF'], 'size' => 9],
        ]);
        $sheet->mergeCells("A{$noteRow}:E{$noteRow}");

        // ── Stream the file to the browser ─────────────────────────────────────
        $filename = 'invitations-template.xlsx';
        $writer   = new XlsxWriter($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function showByToken($token)
    {
        $guest = EventInvitation::query()
            ->with('event')
            ->where('invitation_token', $token)
            ->firstOrFail();

        $event = $this->resolveEventForInvitation($guest);

        return view('home.events.rsvp-page', compact('guest', 'event'));
    }

    public function submit(Request $request, string $token, QrCodeService $qrService)
    {
        try {
            Log::info('RSVP DEBUG', [
                'token' => $token,
                'request' => $request->all(),
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);

            $guest = EventInvitation::where('invitation_token', $token)->firstOrFail();

            if ($guest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم الرد على هذه الدعوة مسبقًا'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'response_status' => 'required|in:accepted,declined,maybe',
                'guests_count' => 'nullable|integer|min:0|max:' . $guest->allowed_guests,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $guest->update([
                'status' => $request->response_status,
                'responded_at' => now(),
                'selected_guests' => $request->response_status === 'accepted'
                    ? ($request->guests_count ?? 0)
                    : 0,
            ]);

            if (in_array($request->response_status, ['accepted', 'maybe'])) {
                $tickets = [];

                for ($i = 0; $i <= $guest->selected_guests; $i++) {
                    $qrToken = (string) Str::uuid();
                    $type = $i === 0 ? 'Main' : 'Guest';

                    InvitationQr::create([
                        'event_invitation_id' => $guest->id,
                        'type' => strtolower($type),
                        'token' => $qrToken,
                    ]);

                    $tickets[] = [
                        'label' => $type,
                        'qr' => $qrService->generateBase64($qrToken),
                    ];
                }

                $event = $this->resolveEventForInvitation($guest);

                if (!$event) {
                    throw new \RuntimeException('Unable to resolve the event for this invitation.');
                }

                try {
                    Mail::to($guest->invitee_email)
                        ->send(new TicketMail($guest, $tickets, $event));
                } catch (Throwable $mailErr) {
                    // Log but do not fail the RSVP — the invitation status is already saved.
                    Log::error('Failed to send ticket email after RSVP', [
                        'invitation_id' => $guest->id,
                        'email'         => $guest->invitee_email,
                        'error'         => $mailErr->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully'
            ]);

        } catch (Throwable $e) {
            Log::error('RSVP ERROR', [
                'token' => $token,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $invitation = EventInvitation::findOrFail($id);
        $this->authorizeInvitationForUser($invitation, $request->user());

        InvitationQr::where('event_invitation_id', $id)->delete();
        $invitation->delete();

        return back()->with('success', 'Invitation deleted successfully');
    }

    public function downloadPdf($token, QrCodeService $qrService)
    {
        $invitation = EventInvitation::with(['InvitationQrs', 'event'])
            ->where('invitation_token', $token)
            ->firstOrFail();

        $event = $this->resolveEventForInvitation($invitation);

        $tickets = [];

        foreach ($invitation->InvitationQrs as $qrRecord) {
            $tickets[] = [
                'label' => $qrRecord->type === 'main' ? 'Main' : 'Guest',
                'qr' => $qrService->generateBase64($qrRecord->token),
                'qr_raw' => $qrRecord->token,
            ];
        }

        if (empty($tickets)) {
            return back()->with('error', 'No tickets generated yet.');
        }

        $pdf = Pdf::loadView('tickets', [
            'invitation' => $invitation,
            'tickets' => $tickets,
            'event' => $event,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Event-Ticket-' . $invitation->invitee_name . '.pdf');
    }

    protected function authorizeEventForUser(Event $event, $user)
    {
        if (!$user || $user->isSystemAdmin()) {
            return;
        }

        if ((int) $event->company_id !== (int) $user->company_id) {
            abort(403);
        }
    }

    protected function authorizeInvitationForUser(EventInvitation $invitation, $user)
    {
        if (!$user || $user->isSystemAdmin()) {
            return;
        }

        if ((int) $invitation->company_id !== (int) $user->company_id) {
            abort(403);
        }
    }
}

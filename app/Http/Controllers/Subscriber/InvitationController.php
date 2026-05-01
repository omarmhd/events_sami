<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Mail\InvitationSent;
use App\Services\EventAnalyticsService;
use App\Services\PublicUrlService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function __construct(
        private EventAnalyticsService $analyticsService,
    ) {}

    /**
     * List invitations for an event
     */
    public function list(Request $request, Event $event, SubscriptionService $subscriptionService)
    {
        $this->authorize('view', $event);
        $user = $request->user();

        $activeStatus = (string) $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'accepted', 'declined', 'rejected', 'maybe', 'sent'];
        if (!in_array($activeStatus, $allowedStatuses, true)) {
            $activeStatus = 'all';
        }

        $search = trim((string) $request->input('searchInput', $request->input('search', '')));

        $baseQuery = EventInvitation::query()->where('event_id', $event->id);
        if ($user && !$user->isSystemAdmin() && $user->company_id) {
            $baseQuery->where('company_id', $user->company_id);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('invitee_name', 'like', "%{$search}%")
                    ->orWhere('invitee_email', 'like', "%{$search}%")
                    ->orWhere('invitee_phone', 'like', "%{$search}%")
                    ->orWhere('invitee_position', 'like', "%{$search}%")
                    ->orWhere('invitee_nationality', 'like', "%{$search}%")
                    ->orWhere('invited_name', 'like', "%{$search}%")
                    ->orWhere('invited_email', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $invitationsQuery = (clone $baseQuery)->latest('id');

        if ($activeStatus === 'declined') {
            $invitationsQuery->whereIn('status', ['declined', 'rejected']);
        } elseif ($activeStatus !== 'all') {
            $invitationsQuery->where('status', $activeStatus);
        }

        $invitations = $invitationsQuery->paginate(50);
        $invitations->appends($request->query());

        $statusStats = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $canCsvImport = false;
        $canBulkResend = false;

        if ($user && $user->isSystemAdmin()) {
            $canCsvImport = true;
            $canBulkResend = true;
        } elseif ($user && $user->company) {
            $canCsvImport = $subscriptionService->featureEnabled($user->company, 'csv_import');
            $canBulkResend = $subscriptionService->featureEnabled($user->company, 'bulk_resend');
        }

        $events = Event::query()
            ->when($user && !$user->isSystemAdmin(), function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })
            ->orderByDesc('id')
            ->get();
        $selectedEventId = (int) $event->id;

        $totalInvitations = (int) $statusStats->sum();
        $declinedCount = (int) (($statusStats->get('declined', 0)) + ($statusStats->get('rejected', 0)));

        return view('subscriber.invitations.list', compact(
            'event',
            'events',
            'selectedEventId',
            'invitations',
            'activeStatus',
            'statusStats',
            'totalInvitations',
            'declinedCount',
            'search',
            'canCsvImport',
            'canBulkResend'
        ));
    }

    /**
     * Store bulk invitations from CSV
     */
    public function bulkImport(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $company = Auth::user()->company;
        $subscriptionService = app('SubscriptionManagementService');

        // Check feature access
        if (!$subscriptionService->hasFeatureAccess($company, 'bulk_import_csv')) {
            return response()->json([
                'success' => false,
                'message' => __('invitations.api.feature_pro_only'),
                'action' => 'upgrade',
            ], 403);
        }

        try {
            $file = $request->file('file');
            $data = array_map('str_getcsv', file($file->getPathname()));

            $imported = 0;
            $skipped = 0;

            foreach ($data as $row) {
                if (count($row) < 2) continue; // Skip invalid rows

                [$name, $email, $phone, $nationality] = array_pad($row, 4, null);

                // Check invite limit
                if ($subscriptionService->isInviteLimitExceeded($company, $event->invitations()->count() + 1)) {
                    $skipped++;
                    continue;
                }

                EventInvitation::firstOrCreate(
                    ['event_id' => $event->id, 'invited_email' => $email],
                    [
                        'company_id' => $company->id,
                        'invited_name' => $name,
                        'invited_phone' => $phone,
                        'nationality' => $nationality,
                        'status' => 'pending',
                    ]
                );

                $imported++;
            }

            return response()->json([
                'success' => true,
                'message' => __('invitations.api.import_success', [
                    'imported' => $imported,
                    'skipped' => $skipped,
                ]),
                'imported' => $imported,
                'skipped' => $skipped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('invitations.api.import_failed', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Resend invitation
     */
    public function resend(EventInvitation $invitation)
    {
        $this->authorize('update', $invitation->event);

        try {
            $email = $invitation->invitee_email ?? $invitation->invited_email;
            $event = $invitation->event;

            if (!$email || !$event) {
                return response()->json([
                    'success' => false,
                    'message' => __('invitations.api.resend_failed'),
                ], 422);
            }

            if (!$invitation->invitation_token && !$invitation->token) {
                $invitation->invitation_token = (string) Str::uuid();
                $invitation->save();
            }

            $token = $invitation->invitation_token ?? $invitation->token;
            $company = $invitation->company ?? $event->company ?? null;

            $invitationLink = $company
                ? app(PublicUrlService::class)->rsvpUrl($company, $token)
                : route('rsvp.show', $token);

            Mail::to($email)->send(new InvitationSent($invitation, $invitationLink, $event));

            $invitation->update([
                'last_sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('invitations.api.resend_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('invitations.api.resend_failed'),
            ], 500);
        }
    }

    /**
     * Bulk resend invitations
     */
    public function bulkResend(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $company = Auth::user()->company;
        $subscriptionService = app('SubscriptionManagementService');

        // Check feature access
        if (!$subscriptionService->hasFeatureAccess($company, 'bulk_resend_invitations')) {
            return response()->json([
                'success' => false,
                'message' => __('invitations.api.feature_pro_only'),
            ], 403);
        }

        $statuses = $request->input('statuses', ['pending']);
        $invitations = $event->invitations()
            ->whereIn('status', $statuses)
            ->get();

        foreach ($invitations as $invitation) {
            dispatch(new \App\Jobs\SendInvitationEmail($invitation));
        }

        return response()->json([
            'success' => true,
            'message' => __('invitations.api.bulk_resend_success', ['count' => $invitations->count()]),
            'count' => $invitations->count(),
        ]);
    }

    /**
     * Export invitations to CSV
     */
    public function exportCsv(Event $event)
    {
        $this->authorize('view', $event);

        $csv = $this->analyticsService->exportEventToCsv($event);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="invitations_' . $event->id . '.csv"',
        ]);
    }

    /**
     * Copy direct link to clipboard
     */
    public function copyDirectLink(EventInvitation $invitation)
    {
        $this->authorize('view', $invitation->event);

        $token = $invitation->invitation_token ?? $invitation->token;

        if (!$token) {
            $token = (string) Str::uuid();
            $invitation->invitation_token = $token;
            $invitation->save();
        }

        // Resolve tenant company so the shared link uses the subdomain URL
        // when the company's plan includes the `custom_subdomain` feature.
        $company = $invitation->company ?? $invitation->event?->company ?? null;
        $url = $company
            ? app(PublicUrlService::class)->rsvpUrl($company, $token)
            : route('rsvp.show', ['token' => $token]);

        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => __('invitations.api.copied'),
            'link_text' => __('invitations.api.link_text', ['url' => $url]),
        ]);
    }
}

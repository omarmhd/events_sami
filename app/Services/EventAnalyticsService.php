<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\Ticket;
use App\Models\TicketCheckinLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventAnalyticsService
{
    /**
     * Get comprehensive event statistics
     */
    public function getEventStats(Event $event): array
    {
        $invitations = EventInvitation::where('event_id', $event->id)->get();
        $tickets = Ticket::where('event_id', $event->id)->get();

        $checkedInCount = TicketCheckinLog::whereHas('ticket', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->count();

        return [
            'total_invitations' => $invitations->count(),
            'accepted_invitations' => $invitations->where('status', 'accepted')->count(),
            'rejected_invitations' => $invitations->where('status', 'rejected')->count(),
            'pending_invitations' => $invitations->where('status', 'pending')->count(),
            'total_tickets' => $tickets->count(),
            'checked_in' => $checkedInCount,
            'no_show' => $tickets->where('status', '!=', 'checked_in')->count(),
            'check_in_rate' => $tickets->count() > 0 ? round(($checkedInCount / $tickets->count()) * 100, 2) : 0,
            'acceptance_rate' => $invitations->count() > 0 ? round(($invitations->where('status', 'accepted')->count() / $invitations->count()) * 100, 2) : 0,
        ];
    }

    /**
     * Get company dashboard statistics
     */
    public function getCompanyDashboardStats($company)
    {
        $events = Event::where('company_id', $company->id)->get();
        $year = Carbon::now()->year;

        $totalInvitations = EventInvitation::whereHas('event', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->count();

        $totalTickets = Ticket::whereHas('event', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->count();

        $checkedInTickets = Ticket::whereHas('event', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->where('status', 'checked_in')->count();

        $acceptedInvitations = EventInvitation::whereHas('event', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->where('status', 'accepted')->count();

        return [
            'total_events' => $events->count(),
            'upcoming_events' => $events->where('start_datetime', '>=', now())->count(),
            'past_events' => $events->where('start_datetime', '<', now())->count(),
            'total_invitations' => $totalInvitations,
            'accepted_invitations' => $acceptedInvitations,
            'total_tickets' => $totalTickets,
            'checked_in_count' => $checkedInTickets,
            'overall_attendance_rate' => $totalTickets > 0 ? round(($checkedInTickets / $totalTickets) * 100, 2) : 0,
        ];
    }

    /**
     * Export event data to CSV
     */
    public function exportEventToCsv(Event $event): string
    {
        $invitations = EventInvitation::where('event_id', $event->id)->get();

        $csv = "الاسم,البريد الإلكتروني,الحالة,تاريخ الرد,الجنسية\n";

        foreach ($invitations as $invitation) {
            $csv .= "\"" . $invitation->invited_name . "\",";
            $csv .= "\"" . $invitation->invited_email . "\",";
            $csv .= "\"" . $this->getStatusLabel($invitation->status) . "\",";
            $csv .= "\"" . ($invitation->responded_at ? $invitation->responded_at->format('Y-m-d H:i') : '') . "\",";
            $csv .= "\"" . ($invitation->nationality ?? '') . "\"\n";
        }

        return $csv;
    }

    /**
     * Get attendance report
     */
    public function getAttendanceReport(Event $event): array
    {
        $checkins = TicketCheckinLog::whereHas('ticket', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->with('ticket')
            ->orderBy('checked_in_at', 'desc')
            ->get();

        return [
            'event_name' => $event->name,
            'event_date' => $event->start_datetime->format('Y-m-d H:i'),
            'total_checked_in' => $checkins->count(),
            'checkins' => $checkins->map(function ($checkin) {
                return [
                    'name' => $checkin->ticket->attendee_name,
                    'email' => $checkin->ticket->attendee_email,
                    'checked_in_at' => $checkin->checked_in_at->format('Y-m-d H:i:s'),
                    'notes' => $checkin->notes,
                ];
            })->toArray(),
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'معلقة',
            'accepted' => 'مقبولة',
            'rejected' => 'مرفوضة',
            'cancelled' => 'ملغاة',
            default => $status,
        };
    }
}

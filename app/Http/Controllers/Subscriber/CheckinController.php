<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Models\InvitationQr;
use App\Models\Ticket;
use App\Models\TicketCheckinLog;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    /**
     * Show check-in page with QR scanner
     */
    public function showCheckinPage($eventSlug)
    {
        $event = \App\Models\Event::where('event_slug', $eventSlug)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        return view('subscriber.checkin.scanner', ['event' => $event]);
    }

    /**
     * Process QR code scan
     */
    public function processQrScan(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'event_id' => 'required|integer',
        ]);

        $event = \App\Models\Event::findOrFail($validated['event_id']);
        $this->authorize('update', $event);

        $qrData = $validated['qr_data'];

        // Find QR record
        $qrRecord = InvitationQr::where('token', $qrData)->first();

        if (!$qrRecord) {
            return response()->json([
                'success' => false,
                'status' => 'invalid',
                'message' => 'كود غير صحيح',
            ]);
        }

        $ticket = $qrRecord->ticket;

        if (!$ticket || $ticket->event_id !== $event->id) {
            return response()->json([
                'success' => false,
                'status' => 'invalid_event',
                'message' => 'هذا الكود لا ينتمي لهذا الحدث',
            ]);
        }

        // Check if already checked in
        if ($ticket->status === 'checked_in') {
            return response()->json([
                'success' => true,
                'status' => 'already_checked',
                'message' => 'تم تسجيل الدخول مسبقاً',
                'ticket' => [
                    'name' => $ticket->attendee_name,
                    'email' => $ticket->attendee_email,
                    'checked_in_at' => $ticket->checked_in_at,
                ],
            ]);
        }

        if ($ticket->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'status' => 'cancelled',
                'message' => 'هذه التذكرة ملغاة',
            ]);
        }

        // Check if event allows re-entry
        $existingCheckin = TicketCheckinLog::where('ticket_id', $ticket->id)->exists();
        if ($existingCheckin && !$event->allow_reentry) {
            return response()->json([
                'success' => false,
                'status' => 'reentry_not_allowed',
                'message' => 'إعادة الدخول غير مسموح بها',
            ]);
        }

        // Process check-in
        $ticket->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        TicketCheckinLog::create([
            'ticket_id' => $ticket->id,
            'checked_in_by' => auth()->id(),
            'checked_in_at' => now(),
            'qr_token' => $qrData,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'checked_in',
            'message' => 'تم تسجيل الدخول بنجاح',
            'ticket' => [
                'name' => $ticket->attendee_name,
                'email' => $ticket->attendee_email,
                'checked_in_at' => now()->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Get check-in statistics for event
     */
    public function getCheckinStats($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $this->authorize('view', $event);

        $total = Ticket::where('event_id', $event->id)->count();
        $checkedIn = Ticket::where('event_id', $event->id)
            ->where('status', 'checked_in')
            ->count();

        return response()->json([
            'total' => $total,
            'checked_in' => $checkedIn,
            'pending' => $total - $checkedIn,
            'percentage' => $total > 0 ? round(($checkedIn / $total) * 100, 2) : 0,
        ]);
    }

    /**
     * Get recent check-ins
     */
    public function getRecentCheckins($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $this->authorize('view', $event);

        $checkins = TicketCheckinLog::whereHas('ticket', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })
            ->with('ticket')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($checkin) {
                return [
                    'name' => $checkin->ticket->attendee_name,
                    'time' => $checkin->checked_in_at->format('H:i:s'),
                    'email' => $checkin->ticket->attendee_email,
                ];
            });

        return response()->json([
            'checkins' => $checkins,
        ]);
    }
}

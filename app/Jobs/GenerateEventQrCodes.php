<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateEventQrCodes implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Event $event,
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $invitations = EventInvitation::where('event_id', $this->event->id)
            ->where('status', 'accepted')
            ->whereNull('qr_code_generated_at')
            ->get();

        foreach ($invitations as $invitation) {
            // Generate unique QR token
            $token = \Illuminate\Support\Str::random(32);

            // Create or update InvitationQr record
            \App\Models\InvitationQr::updateOrCreate(
                ['invitation_id' => $invitation->id],
                [
                    'token' => $token,
                    'qr_data' => json_encode([
                        'invitation_id' => $invitation->id,
                        'event_id' => $this->event->id,
                        'token' => $token,
                    ]),
                ]
            );

            // Create or update ticket
            $ticket = Ticket::updateOrCreate(
                ['invitation_id' => $invitation->id, 'event_id' => $this->event->id],
                [
                    'company_id' => $this->event->company_id,
                    'ticket_number' => 'TKT-' . $this->event->id . '-' . $invitation->id,
                    'attendee_name' => $invitation->invited_name,
                    'attendee_email' => $invitation->invited_email,
                    'attendee_phone' => $invitation->invited_phone,
                    'status' => 'pending',
                    'qr_token' => $token,
                ]
            );

            $invitation->update([
                'qr_code_generated_at' => now(),
            ]);
        }
    }
}

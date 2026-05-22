<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// ShouldQueue removed: TicketMail is sent synchronously via Mail::send() inside
// the RSVP submit handler. Queuing it causes silent failures under the sync driver.
class TicketMail extends Mailable
{
    use SerializesModels;

    public $invitation;
    public $tickets;
    public $event;

    public function __construct($invitation, $tickets, $event)
    {
        $this->invitation = $invitation;
        $this->tickets = $tickets;
        $this->event = $event;
    }

    public function build()
    {
        $email = $this->subject('Your Event Tickets')
            ->view('emails.tickets')
            ->with([
                'invitation' => $this->invitation,
                'tickets' => $this->tickets,
                'event' => $this->event,
            ]);

        foreach ($this->tickets as $index => $ticket) {
            $qrData = (string) ($ticket['qr'] ?? '');
            $parts = explode(',', $qrData, 2);

            if (count($parts) !== 2 || $parts[1] === '') {
                continue;
            }

            $email->attachData(
                base64_decode($parts[1]),
                ($ticket['label'] ?? 'ticket') . '.png',
                [
                    'mime' => 'image/png',
                    'content_id' => 'ticket' . $index,
                ]
            );
        }

        return $email;
    }
}

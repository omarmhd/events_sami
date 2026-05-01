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
        // confirmation_email_subject column was removed from events table.
        // Subject is now always 'Invitation Confirmed' (or overridden by EmailTemplate system).
        $subject = 'Invitation Confirmed';

        // QR codes are embedded directly in the HTML as base64 data URIs.
        // No attachments needed — the view renders them inline via <img src="data:image/png;base64,...">
        return $this->subject($subject)
            ->view('emails.tickets')
            ->with([
                'invitation' => $this->invitation,
                'tickets' => $this->tickets,
                'event' => $this->event,
            ]);
    }
}

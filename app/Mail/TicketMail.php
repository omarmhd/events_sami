<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

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
        $company = $this->event->company ?: ($this->invitation->company ?? null);

        if ($company) {
            $compiled = app(EmailTemplateService::class)->compile(
                $company,
                $this->event,
                EmailTemplate::TYPE_TICKET,
                [
                    'guest_name' => $this->invitation->invitee_name ?? $this->invitation->guest_name ?? 'Guest',
                    'guest_email' => $this->invitation->invitee_email ?? $this->invitation->guest_email ?? '',
                    'invitee_position' => $this->invitation->invitee_position ?? '',
                    'allowed_guests' => (int) ($this->invitation->allowed_guests ?? 0),
                    'selected_guests' => (int) ($this->invitation->selected_guests ?? 0),
                    'tickets_count' => count($this->tickets),
                    'rsvp_status' => $this->invitation->status ?? '',
                    'response_time' => optional($this->invitation->responded_at ?? null)->format('Y-m-d H:i') ?: now()->format('Y-m-d H:i'),
                ]
            );

            $email = $this->subject($compiled['subject'] ?? 'Your Event Tickets')
                ->from($compiled['from_email'] ?? config('mail.from.address'), $compiled['from_name'] ?? config('app.name'))
                ->view('emails.tickets')
                ->with([
                    'invitation' => $this->invitation,
                    'tickets' => $this->tickets,
                    'event' => $this->event,
                ]);

            if (!empty($compiled['reply_to'])) {
                $email->replyTo($compiled['reply_to']);
            }

            return $email;
        }

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

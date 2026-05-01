<?php

namespace App\Mail;

use App\Models\EventInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tickets;
    public $employee;
    public $event;

    public function __construct($tickets, $employee, $event)
    {
        $this->tickets = $tickets;
        $this->employee = $employee;
        $this->event = $event;
    }

    public function build()
    {
        $generatedTickets = [];
        foreach ($this->tickets as $ticket) {
            $file = $ticket->barcode ? storage_path('app/public/qr_codess/' . $ticket->barcode) : null;
            if ($file && file_exists($file)) {
                $data = base64_encode(file_get_contents($file));
                $generatedTickets[] = [
                    'label' => $ticket->type === 'employee' ? 'Main' : 'Guest',
                    'qr' => 'data:image/png;base64,' . $data,
                ];
            }
        }

        $invitation = new EventInvitation([
            'invitee_name' => $this->employee->name,
            'invitee_email' => $this->employee->email,
            'invitee_position' => $this->employee->position,
            'status' => 'accepted',
        ]);

        $customSubject = trim((string) ($this->event->confirmation_email_subject ?? ''));
        $subject = $customSubject !== '' ? $customSubject : 'Invitation Confirmed';

        $mail = $this->subject($subject)
            ->view('emails.tickets')
            ->with([
                'invitation' => $invitation,
                'tickets' => $generatedTickets,
                'event' => $this->event,
            ]);

        foreach ($generatedTickets as $ticket) {
            if (!empty($ticket['qr'])) {
                $parts = explode(',', $ticket['qr'], 2);
                if (count($parts) === 2) {
                    $mail->attachData(
                        base64_decode($parts[1]),
                        ($ticket['label'] ?? 'ticket') . '.png',
                        ['mime' => 'image/png']
                    );
                }
            }
        }

        return $mail;
    }
}

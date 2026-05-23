<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Services\EmailTemplateService;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Note: ShouldQueue intentionally removed. Invitation emails are sent synchronously
// via Mail::send() in the controller, or through the SendInvitationEmail job (which
// handles its own queuing). Implementing ShouldQueue here while calling Mail::send()
// causes double-queuing behaviour under certain queue drivers.
class InvitationSent extends Mailable
{
    use SerializesModels;

    public EventInvitation $invitation;
    public string $invitationLink;
    public Event $event;

    public function __construct(EventInvitation $invitation, string $invitationLink, Event $event)
    {
        $this->invitation = $invitation;
        $this->invitationLink = $invitationLink;
        $this->event = $event;
    }

    public function build()
    {
        $company = $this->event->company ?: $this->invitation->company;
        $eventTitle = $this->event->title ?: $this->event->name;
        // invitation_email_subject column was removed from the events table; subject is
        // now derived from the event title or the compiled template subject.
        $fallbackSubject = 'You are invited! - ' . $eventTitle;

        if (!$company) {
            return $this->subject($fallbackSubject)
                ->view('emails.invitation');
        }

        $compiled = app(EmailTemplateService::class)->compile(
            $company,
            $this->event,
            \App\Models\EmailTemplate::TYPE_INVITATION,
            [
                'guest_name' => $this->invitation->invitee_name,
                'guest_email' => $this->invitation->invitee_email,
                'invitee_position' => $this->invitation->invitee_position,
                'invitation_link' => $this->invitationLink,
                'invitation_sent_at' => optional($this->invitation->last_sent_at)->format('Y-m-d H:i') ?: now()->format('Y-m-d H:i'),
                'allowed_guests' => (int) ($this->invitation->allowed_guests ?? 0),
            ]
        );

        $htmlContent = (string) ($compiled['html'] ?? '');
        $visibleContent = trim(strip_tags($htmlContent));
        if ($visibleContent === '') {
            return $this->subject($fallbackSubject)
                ->view('emails.invitation')
                ->with([
                    'invitation' => $this->invitation,
                    'invitationLink' => $this->invitationLink,
                    'event' => $this->event,
                    'email_vars' => $compiled['variables'] ?? [],
                    'compiled_email' => $compiled,
                ]);
        }

        $compiledSubject = (string) ($compiled['subject'] ?? '');
        $mail = $this->subject($compiledSubject !== '' ? $compiledSubject : $fallbackSubject)
            ->from($compiled['from_email'], $compiled['from_name'])
            ->view('emails.dynamic')
            ->with([
                'htmlContent' => $htmlContent,
            ]);

        if (!empty($compiled['reply_to'])) {
            $mail->replyTo($compiled['reply_to']);
        }

        return $mail;
    }
}

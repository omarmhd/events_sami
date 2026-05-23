<?php

namespace App\Jobs;

use App\Mail\InvitationSent;
use App\Models\EventInvitation;
use App\Services\PublicUrlService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendInvitationEmail implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        private EventInvitation $invitation,
    ) {
        $this->onQueue('default');
        $this->delay(now()->addSeconds(5));
    }

    public function handle(): void
    {
        $email = $this->invitation->invitee_email ?? $this->invitation->invited_email;
        $event = $this->invitation->event;

        if (!$email || !$event) {
            return;
        }

        if (!$this->invitation->invitation_token && !$this->invitation->token) {
            $this->invitation->invitation_token = (string) Str::uuid();
            $this->invitation->save();
        }

        $token   = $this->invitation->invitation_token ?? $this->invitation->token;
        $company = $this->invitation->company ?? $event->company ?? null;

        // Use PublicUrlService so the link uses the tenant subdomain when
        // the company's plan has the `custom_subdomain` feature enabled.
        $invitationLink = $company
            ? app(PublicUrlService::class)->rsvpUrl($company, $token)
            : route('rsvp.show', $token);

        Mail::to($email)->send(new InvitationSent($this->invitation, $invitationLink, $event));

        $this->invitation->update([
            'last_sent_at' => now(),
        ]);
    }
}

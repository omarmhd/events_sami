<?php

namespace App\Mail;

use App\Models\CompanySubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private CompanySubscription $subscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تجديد اشتراكك في منصة Maan Invite يقترب',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-renewal-reminder',
            with: [
                'company' => $this->subscription->company,
                'planName' => $this->subscription->plan->name ?? 'الخطة',
                'renewalDate' => $this->subscription->ends_at->format('Y-m-d'),
                'renewalUrl' => route('subscription.show'),
            ],
        );
    }
}

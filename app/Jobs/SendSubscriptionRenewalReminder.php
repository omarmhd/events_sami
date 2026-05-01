<?php

namespace App\Jobs;

use App\Models\CompanySubscription;
use App\Services\SubscriptionManagementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionRenewalReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private CompanySubscription $subscription,
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        // Send renewal reminder 14 days before expiration
        if ($this->subscription->ends_at) {
            $daysUntilExpiry = now()->diffInDays($this->subscription->ends_at);

            if ($daysUntilExpiry === 14) {
                Mail::to($this->subscription->company->owner->email)
                    ->send(new \App\Mail\SubscriptionRenewalReminder($this->subscription));
            }
        }
    }
}

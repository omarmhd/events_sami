<?php

namespace App\Console\Commands;

use App\Models\CompanySubscription;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessSubscriptionRenewals extends Command
{
    protected $signature = 'subscriptions:process-renewals';
    protected $description = 'Send 14-day renewal reminders and auto-renew annual subscriptions.';

    public function handle(BillingService $billingService, SubscriptionService $subscriptionService)
    {
        $today = Carbon::today();

        $reminders = CompanySubscription::query()
            ->where('status', 'active')
            ->whereNotNull('renews_at')
            ->whereDate('renews_at', $today->copy()->addDays(14))
            ->with(['company', 'plan'])
            ->get();

        foreach ($reminders as $subscription) {
            $company = $subscription->company;
            if (!$company || !$company->billing_email) {
                continue;
            }

            Mail::raw(
                "Your subscription will renew on {$subscription->renews_at->format('Y-m-d')}.",
                function ($message) use ($company) {
                    $message->to($company->billing_email)
                        ->subject('Subscription renewal reminder');
                }
            );
        }

        $renewals = CompanySubscription::query()
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('renews_at')
            ->whereDate('renews_at', '<=', $today)
            ->with(['company', 'plan'])
            ->get();

        foreach ($renewals as $subscription) {
            if (!$subscription->company || !$subscription->plan) {
                continue;
            }

            $newSubscription = $subscriptionService->switchCompanyPlan(
                $subscription->company,
                $subscription->plan,
                'active'
            );

            $amount = (float) $subscription->plan->annual_price;
            if ($amount > 0) {
                $billingService->createAnnualInvoice($subscription->company, $newSubscription, $amount);
            }
        }

        $this->info('Subscription renewal processing complete.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionManagementService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionLimits
{
    public function handle(Request $request, Closure $next, string $mode = 'check_limits')
    {
        $user = $request->user();

        if (!$user || !$user->company) {
            return $next($request);
        }

        $subscriptionService = app(SubscriptionManagementService::class);
        $company     = $user->company;
        $subscription = $company->activeSubscription;

        // ── 1. Company suspended by admin ─────────────────────────────────────
        if ($company->status === 'suspended') {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'suspended',
            ]);
        }

        // ── 2. No subscription at all ─────────────────────────────────────────
        if (!$subscription) {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'no_subscription',
            ]);
        }

        // ── 3. Trial expired ──────────────────────────────────────────────────
        if ($subscriptionService->isTrialExpired($subscription)) {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'trial_expired',
            ]);
        }

        // ── 4. Subscription explicitly cancelled / expired ────────────────────
        if (in_array($subscription->status, ['cancelled', 'expired'])) {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'subscription_ended',
            ]);
        }

        // ── 5. Subscription end date has passed (active plan but lapsed) ──────
        $endDate = $subscription->ends_at ?? $subscription->renews_at ?? null;
        if ($endDate && Carbon::parse($endDate)->isPast() && $subscription->status === 'active') {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'subscription_ended',
            ]);
        }

        // ── 6. Event quota exceeded ───────────────────────────────────────────
        if ($subscriptionService->isEventLimitExceeded($company)) {
            return redirect()->route('subscription.show-paywall', [
                'reason' => 'event_limit',
            ]);
        }

        return $next($request);
    }
}

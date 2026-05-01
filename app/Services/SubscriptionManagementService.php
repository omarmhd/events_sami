<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionNeedsAssessment;
use App\Support\FeatureRegistry;
use Carbon\Carbon;

class SubscriptionManagementService
{
    /**
     * Initialize free trial for new company
     */
    public function initializeFreeTrial(Company $company): CompanySubscription
    {
        $trialEndsAt = Carbon::now()->addDays(15);

        return CompanySubscription::create([
            'company_id' => $company->id,
            'plan_id' => 1, // Assuming free plan has ID 1
            'status' => 'trial',
            'trial_started_at' => Carbon::now(),
            'trial_ends_at' => $trialEndsAt,
            'annual_events_limit' => 2, // 1 public + 1 private
            'max_invites_per_event' => 10,
        ]);
    }

    /**
     * Get remaining trial days
     */
    public function getRemainingTrialDays(CompanySubscription $subscription): int
    {
        if (!$subscription->trial_ends_at) {
            return 0;
        }

        return max(0, $subscription->trial_ends_at->diffInDays(Carbon::now(), false));
    }

    /**
     * Check if trial has expired
     */
    public function isTrialExpired(CompanySubscription $subscription): bool
    {
        if ($subscription->status !== 'trial') {
            return false;
        }

        return Carbon::now()->isAfter($subscription->trial_ends_at);
    }

    /**
     * Recommend plan based on needs assessment
     */
    public function recommendPlan(SubscriptionNeedsAssessment $assessment): string
    {
        // If requires custom development -> Enterprise
        if ($assessment->requires_custom_development) {
            return 'enterprise';
        }

        // If large number of events without custom -> Professional
        if ($assessment->annual_events_estimate > 12 || $assessment->average_attendance > 100) {
            return 'professional';
        }

        // Default to Starter
        return 'starter';
    }

    /**
     * Check whether a feature is accessible for a company's current plan.
     *
     * Delegates entirely to SubscriptionPlan::hasFeature() via FeatureRegistry normalization.
     * The JSON `features` column is the single source of truth — no hardcoded plan-code arrays.
     *
     * @param  Company  $company
     * @param  string   $featureCode  Canonical key or any registered alias
     * @return bool
     */
    public function hasFeatureAccess(Company $company, string $featureCode): bool
    {
        $subscription = $company->activeSubscription;

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        $normalizedKey = FeatureRegistry::normalize($featureCode);
        return $subscription->plan->hasFeature($normalizedKey);
    }

    /**
     * Get usage limits for a subscription from the actual plan data.
     * Reads from the plan's database columns — no hardcoded values.
     */
    public function getUsageLimits(CompanySubscription $subscription): array
    {
        $plan = $subscription->plan;

        return [
            'annual_events'        => $subscription->annual_event_quota
                                      ?? $plan?->annual_event_limit
                                      ?? null,   // null = unlimited
            'max_invites_per_event'=> $plan?->per_event_invitee_limit ?? null,
            'custom_domain'        => $plan ? $plan->hasFeature('white_label') : false,
        ];
    }

    /**
     * Calculate prorated upgrade cost
     */
    public function calculateProratedUpgradeCost(CompanySubscription $currentSubscription, string $newPlanCode): float
    {
        // Simple calculation - can be enhanced
        $daysRemaining = $currentSubscription->ends_at?->diffInDays(Carbon::now()) ?? 0;
        $currentPrice = $currentSubscription->plan?->annual_price ?? 0;
        $newPrice = 0; // Fetch from new plan

        $dailyDifference = ($newPrice - $currentPrice) / 365;
        return max(0, $dailyDifference * max(0, $daysRemaining));
    }

    /**
     * Check if the company has exceeded its annual event quota.
     *
     * Uses the subscription's tracked counter (annual_events_used) against
     * the quota stored on the subscription (annual_event_quota), falling back
     * to the plan column. A null quota means unlimited.
     */
    public function isEventLimitExceeded(Company $company): bool
    {
        $subscription = $company->activeSubscription;
        if (!$subscription) {
            return true;
        }

        // Trial handled separately in SubscriptionService::canCreateEvent()
        if ($subscription->status === 'trial') {
            return false;
        }

        $quota = $subscription->annual_event_quota
            ?? $subscription->plan?->annual_event_limit
            ?? null;

        if ($quota === null) {
            return false; // Unlimited
        }

        return (int) ($subscription->annual_events_used ?? 0) >= (int) $quota;
    }

    /**
     * Check if an additional invite count would exceed the per-event limit.
     * A null limit means unlimited.
     */
    public function isInviteLimitExceeded(Company $company, int $inviteCount): bool
    {
        $subscription = $company->activeSubscription;
        if (!$subscription) {
            return true;
        }

        $limit = $subscription->plan?->per_event_invitee_limit ?? null;

        if ($limit === null) {
            return false; // Unlimited
        }

        return $inviteCount > (int) $limit;
    }
}

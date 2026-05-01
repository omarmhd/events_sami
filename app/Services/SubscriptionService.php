<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Event;
use App\Models\SubscriptionPlan;
use App\Support\FeatureRegistry;
use Carbon\Carbon;

class SubscriptionService
{
    const TRIAL_DAYS = 15;
    const TRIAL_MAX_INVITEES_PER_EVENT = 10;

    protected function trialDays(): int
    {
        return (int) config('subscription.trial.days', self::TRIAL_DAYS);
    }

    protected function trialInviteeLimit(): int
    {
        return (int) config('subscription.trial.invites_limit', self::TRIAL_MAX_INVITEES_PER_EVENT);
    }

    public function ensureTrialPlan()
    {
        return SubscriptionPlan::firstOrCreate(
            ['code' => 'trial'],
            [
                'name' => 'Trial',
                'description' => sprintf(
                    '%d-day trial with one private and one public event and up to %d invitees per event.',
                    $this->trialDays(),
                    $this->trialInviteeLimit()
                ),
                'annual_price' => 0,
                'per_event_price' => 0,
                'annual_event_limit' => 2,
                'per_event_invitee_limit' => $this->trialInviteeLimit(),
                'includes_csv_import' => false,
                'includes_bulk_resend' => false,
                'includes_customization' => false,
                'highlight_label' => null,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }

    public function ensureCompanySubscription(Company $company)
    {
        // Prefer active/past_due over trial — a paid subscription should always win.
        // This prevents a stale trial row from being returned after an upgrade.
        $subscription = $company->subscriptions()
            ->whereIn('status', ['active', 'past_due'])
            ->latest('id')
            ->first();

        if (!$subscription) {
            $subscription = $company->subscriptions()
                ->where('status', 'trial')
                ->latest('id')
                ->first();
        }

        if ($subscription) {
            return $subscription->load('plan');
        }

        $trialPlan = $this->ensureTrialPlan();

        $trialStart = Carbon::now();
    $trialEnd = (clone $trialStart)->addDays($this->trialDays());

        $subscription = CompanySubscription::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $trialPlan->id,
            'status' => 'trial',
            'started_at' => $trialStart,
            'trial_ends_at' => $trialEnd,
            'annual_event_quota' => $trialPlan->annual_event_limit,
            'annual_events_used' => 0,
            'metadata' => [
                'trial_event_limits' => [
                    'private' => 1,
                    'public' => 1,
                ],
                'trial_invitee_limit' => $this->trialInviteeLimit(),
            ],
        ]);

        $company->update([
            'trial_started_at' => $trialStart,
            'trial_ends_at' => $trialEnd,
            'current_plan_code' => 'trial',
        ]);

        return $subscription->load('plan');
    }

    public function activeSubscriptionFor(Company $company)
    {
        return $this->ensureCompanySubscription($company);
    }

    public function isTrialExpired(CompanySubscription $subscription)
    {
        return $subscription->status === 'trial'
            && $subscription->trial_ends_at
            && $subscription->trial_ends_at->isPast();
    }

    public function canCreateEvent(Company $company, $eventType)
    {
        $subscription = $this->activeSubscriptionFor($company);

        if ($this->isTrialExpired($subscription)) {
            return [
                'allowed' => false,
                'reason' => 'trial_expired',
                'message' => 'Your trial ended. Upgrade to continue creating events.',
            ];
        }

        if ($subscription->status === 'trial') {
            $countByType = Event::query()
                ->where('company_id', $company->id)
                ->where('event_type', $eventType)
                ->count();

            if ($countByType >= 1) {
                return [
                    'allowed' => false,
                    'reason' => 'trial_event_type_limit',
                    'message' => 'Trial allows one event per type (one private and one public).',
                ];
            }

            return ['allowed' => true];
        }

        $limit = $subscription->annual_event_quota;

        if ($limit === null && $subscription->plan) {
            $limit = $subscription->plan->annual_event_limit;
        }

        if ($limit !== null && $subscription->annual_events_used >= $limit) {
            return [
                'allowed' => false,
                'reason' => 'annual_quota_exhausted',
                'message' => 'You used all annual events for your plan. Upgrade to continue.',
            ];
        }

        return ['allowed' => true];
    }

    public function canAddInvitees(Company $company, Event $event, $additionalCount)
    {
        $subscription = $this->activeSubscriptionFor($company);

        if ($this->isTrialExpired($subscription)) {
            return [
                'allowed' => false,
                'reason' => 'trial_expired',
                'message' => 'Trial ended. Upgrade to add more invitees.',
            ];
        }

        $currentInvitees = $event->invitations()->count();

        if ($subscription->status === 'trial') {
            if (($currentInvitees + $additionalCount) > $this->trialInviteeLimit()) {
                return [
                    'allowed' => false,
                    'reason' => 'trial_invitee_limit',
                    'message' => 'Trial invitee limit for this event has been reached. Upgrade to continue.',
                ];
            }

            return ['allowed' => true];
        }

        $limit = $subscription->plan ? $subscription->plan->per_event_invitee_limit : null;

        if ($limit !== null && ($currentInvitees + $additionalCount) > $limit) {
            return [
                'allowed' => false,
                'reason' => 'plan_invitee_limit',
                'message' => 'This plan reached the invitee limit for this event.',
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Check whether a specific feature is enabled for a company's current plan.
     *
     * Uses the FeatureRegistry to normalize aliases (e.g. 'bulk_import_csv' → 'csv_import'),
     * then delegates to SubscriptionPlan::hasFeature() which reads from the JSON `features`
     * column (falling back to the legacy boolean columns if no JSON features are configured).
     *
     * @param  Company  $company
     * @param  string   $feature  Any canonical key or alias from FeatureRegistry
     * @return bool
     */
    public function featureEnabled(Company $company, $feature): bool
    {
        $subscription = $this->activeSubscriptionFor($company);

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        // Normalize alias → canonical key, then delegate to the plan model
        $normalizedKey = FeatureRegistry::normalize($feature);
        return $subscription->plan->hasFeature($normalizedKey);
    }

    /**
     * Return the numeric limit for a feature on the company's current plan.
     * Returns null if unlimited or not found.
     */
    public function featureLimit(Company $company, string $feature): ?int
    {
        $subscription = $this->activeSubscriptionFor($company);

        if (!$subscription || !$subscription->plan) {
            return 0;
        }

        $normalizedKey = FeatureRegistry::normalize($feature);
        return $subscription->plan->featureLimit($normalizedKey);
    }

    public function markEventConsumed(Company $company)
    {
        $subscription = $this->activeSubscriptionFor($company);

        if ($subscription->status !== 'trial') {
            $subscription->increment('annual_events_used');
        }
    }

    public function recommendPlanCode($annualEvents, $averageAttendance, $needsCustomization)
    {
        if ($needsCustomization) {
            return 'enterprise';
        }

        if ($annualEvents >= 12 || $averageAttendance >= 250) {
            return 'professional';
        }

        return 'starter';
    }

    public function switchCompanyPlan(Company $company, SubscriptionPlan $plan, $status)
    {
        $company->subscriptions()
            ->whereIn('status', ['trial', 'active', 'past_due'])
            ->update([
                'status' => 'expired',
                'ends_at' => Carbon::now(),
            ]);

        $start = Carbon::now();

        $subscription = CompanySubscription::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'status' => $status,
            'started_at' => $start,
            'trial_ends_at' => $status === 'trial' ? (clone $start)->addDays($this->trialDays()) : null,
            'renews_at' => $status === 'active' ? (clone $start)->addDays(365) : null,
            'annual_event_quota' => $plan->annual_event_limit,
            'annual_events_used' => 0,
        ]);

        $company->update([
            'current_plan_code' => $plan->code,
            'trial_started_at'  => $status === 'trial' ? $start : $company->trial_started_at,
            // When upgrading to a paid plan, clear trial_ends_at so views never
            // confuse the old trial expiry with the new plan's end date.
            'trial_ends_at'     => $status === 'trial' ? (clone $start)->addDays($this->trialDays()) : null,
        ]);

        return $subscription;
    }

    public function trialEndsInDays(Company $company)
    {
        if (!$company->trial_ends_at) {
            return null;
        }

        return Carbon::now()->diffInDays($company->trial_ends_at, false);
    }
}

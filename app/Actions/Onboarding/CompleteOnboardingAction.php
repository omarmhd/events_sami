<?php

namespace App\Actions\Onboarding;

use App\Models\Company;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class CompleteOnboardingAction
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    public function execute(User $user, array $data): Company
    {
        $company = $user->company;
        $preferredPlanCode = $data['preferred_plan_code'] ?? null;
        $trialDays = (int) config('subscription.trial.days', SubscriptionService::TRIAL_DAYS);
        $companySettings = array_merge($company?->settings ?? [], [
            'preferred_plan_code' => $preferredPlanCode,
            'team_permissions_mode' => 'settings_defined_later',
        ]);

        if (!$company) {
            $company = Company::create([
                'name' => $data['company_name'],
                'contact_email' => $user->email,
                'phone' => $data['phone'],
                'subdomain' => strtolower($data['subdomain']),
                'status' => 'trial',
                'annual_events_estimate' => $data['annual_events_estimate'],
                'trial_started_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays($trialDays),
                'onboarding_completed_at' => Carbon::now(),
                'billing_email' => $user->email,
                'timezone' => $data['timezone'] ?: 'Asia/Riyadh',
                'settings' => $companySettings,
            ]);
        } else {
            $company->update([
                'name' => $data['company_name'],
                'contact_email' => $user->email,
                'phone' => $data['phone'],
                'subdomain' => strtolower($data['subdomain']),
                'annual_events_estimate' => $data['annual_events_estimate'],
                'onboarding_completed_at' => Carbon::now(),
                'timezone' => $data['timezone'] ?: $company->timezone,
                'settings' => $companySettings,
            ]);
        }

        $user->update([
            'organization_id' => $company->id,
            'company_id' => $company->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => 'organizer_owner',
        ]);

        if (!$company->owner_user_id) {
            $company->owner_user_id = $user->id;
            $company->save();
        }

        $this->subscriptionService->ensureCompanySubscription($company);

        return $company;
    }
}


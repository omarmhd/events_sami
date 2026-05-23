<?php

namespace App\Actions\Onboarding;

use App\Models\Company;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompleteOnboardingAction
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    public function execute(User $user, array $data): Company
    {
        $company = $user->company;
        $preferredPlanCode = $data['preferred_plan_code'] ?? null;
        $subdomain = $this->resolveSubdomain(
            $data['subdomain'] ?? null,
            $data['company_name'] ?? $user->name,
            $user->email,
            $company?->id
        );
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
                'subdomain' => $subdomain,
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
                'subdomain' => $subdomain,
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

    private function resolveSubdomain(?string $subdomain, string $companyName, string $email, ?int $companyId = null): string
    {
        $candidate = Str::lower(trim((string) $subdomain));

        if ($candidate === '') {
            $baseSource = $companyName !== '' ? $companyName : Str::before($email, '@');
            $candidate = Str::slug($baseSource, '-');
        }

        $candidate = preg_replace('/[^a-z0-9-]/', '', $candidate) ?: 'company';
        $candidate = trim($candidate, '-');
        $candidate = substr($candidate, 0, 40);
        $candidate = $candidate !== '' ? $candidate : 'company';

        $base = $candidate;
        $index = 1;

        while (
            Company::query()
                ->when($companyId, fn ($query) => $query->whereKeyNot($companyId))
                ->where('subdomain', $candidate)
                ->exists()
        ) {
            $suffix = '-' . $index++;
            $candidate = substr($base, 0, max(1, 40 - strlen($suffix))) . $suffix;
        }

        return $candidate;
    }
}


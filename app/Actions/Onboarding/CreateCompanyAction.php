<?php

namespace App\Actions\Onboarding;

use App\Models\Company;

class CreateCompanyAction
{
    public function __invoke(
        string $name,
        string $subdomain,
        int $ownerUserId,
        int $annualEventsEstimate,
    ): Company {
        return Company::create([
            'name' => $name,
            'subdomain' => $subdomain,
            'owner_user_id' => $ownerUserId,
            'annual_events_estimate' => $annualEventsEstimate,
            'status' => 'active',
            'trial_started_at' => now(),
            'trial_ends_at' => now()->addDays(15),
        ]);
    }
}

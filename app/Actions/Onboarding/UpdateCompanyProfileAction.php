<?php

namespace App\Actions\Onboarding;

use App\Models\Company;

class UpdateCompanyProfileAction
{
    public function __invoke(
        Company $company,
        array $data,
    ): Company {
        $company->update([
            'name' => $data['company_name'] ?? $company->name,
            'phone' => $data['phone'] ?? $company->phone,
            'annual_events_estimate' => $data['annual_events_estimate'] ?? $company->annual_events_estimate,
            'settings' => array_merge(
                $company->settings ?? [],
                $data['settings'] ?? []
            ),
        ]);

        return $company;
    }
}

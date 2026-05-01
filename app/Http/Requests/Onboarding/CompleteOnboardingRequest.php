<?php

namespace App\Http\Requests\Onboarding;

use Illuminate\Foundation\Http\FormRequest;

class CompleteOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $user = $this->user();
        $companyId = $user ? ($user->organization_id ?: $user->company_id) : null;
        $uniqueRule = 'unique:companies,subdomain';

        if ($companyId) {
            $uniqueRule .= ',' . $companyId;
        }

        return [
            'name' => ['required', 'string', 'max:120'],
            'company_name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'annual_events_estimate' => ['required', 'integer', 'min:1', 'max:10000'],
            'subdomain' => ['required', 'alpha_dash', 'min:3', 'max:40', $uniqueRule],
            'timezone' => ['nullable', 'string', 'max:64'],
            'preferred_plan_code' => ['nullable', 'exists:subscription_plans,code'],
        ];
    }
}


<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Configuration
    |--------------------------------------------------------------------------
    |
    | Configure subscription parameters and trial periods
    |
    */

    'trial' => [
        'days' => env('SUBSCRIPTION_TRIAL_DAYS', 15),
        'events_limit' => env('SUBSCRIPTION_TRIAL_EVENTS', 2),
        'invites_limit' => env('SUBSCRIPTION_TRIAL_INVITES', 10),
    ],

    'features' => [
        'bulk_import_csv' => ['professional', 'enterprise'],
        'bulk_resend_invitations' => ['professional', 'enterprise'],
        'custom_branding' => ['professional', 'enterprise'],
        'sso_integration' => ['enterprise'],
        'api_access_basic' => ['professional', 'enterprise'],
        'api_access_full' => ['enterprise'],
        'custom_development' => ['enterprise'],
        'white_label' => ['enterprise'],
        'analytics_ai' => ['enterprise'],
        'webhook_support' => ['enterprise'],
    ],

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 99,
            'billing_cycle' => 'annual',
            'events_limit' => 12,
            'invites_per_event' => 100,
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 299,
            'billing_cycle' => 'annual',
            'events_limit' => 100,
            'invites_per_event' => 1000,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => null,
            'billing_cycle' => 'custom',
            'events_limit' => 99999,
            'invites_per_event' => 99999,
        ],
    ],

    'proration' => [
        'enabled' => true,
        'calculate_daily' => true,
    ],
];

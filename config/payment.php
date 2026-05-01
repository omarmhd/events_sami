<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your payment gateway integration here
    | Supported: 'stripe', 'paypal', 'tap'
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    'gateways' => [
        'stripe' => [
            'key' => env('STRIPE_PUBLIC_KEY'),
            'secret' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'enabled' => env('STRIPE_ENABLED', false),
        ],

        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            'enabled' => env('PAYPAL_ENABLED', false),
        ],

        'tap' => [
            'key' => env('TAP_SECRET_KEY'),
            'public_key' => env('TAP_PUBLIC_KEY'),
            'enabled' => env('TAP_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => env('PAYMENT_CURRENCY', 'SAR'),

    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    */

    'tax_rate' => env('TAX_RATE', 0.15), // 15% VAT in KSA

    /*
    |--------------------------------------------------------------------------
    | Invoice Configuration
    |--------------------------------------------------------------------------
    */

    'invoice' => [
        'prefix' => 'INV',
        'padding' => 6,
        'start_number' => 1001,
    ],
];

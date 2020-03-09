<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Modules\User\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'slack'     => [
        'webhook' => env('SLACK_WEBHOOK_URL'),
        'webhook_invalid_urls' => env('SLACK_WEBHOOK_URL'),
    ],

    'google' => [
        'cloud' => [
            'key' => env('GOOGLE_CLOUD_KEY'),

            'translate_max_length' => env('GOOGLE_TRANSLATE_MAX_LENGTH', 1000),
        ],
        'maps' => [
            'key' => env('GOOGLE_MAP_KEY'),
        ],
    ],
];

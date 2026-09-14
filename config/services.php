<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pesepay' => [
        'integration_key' => env('PESEPAY_INTEGRATION_KEY'),
        'encryption_key' => env('PESEPAY_ENCRYPTION_KEY'),
        'make_payment_url' => env('PESEPAY_MAKE_PAYMENT_URL', 'https://api.pesepay.com/api/payments-engine/v2/payments/make-payment'),
        'check_payment_url' => env('PESEPAY_CHECK_PAYMENT_URL', 'https://api.pesepay.com/api/payments-engine/v1/payments/check-payment'),
        'result_url' => env('PESE_PAY_RESULT_URL'),
        'return_url' => env('PESE_PAY_RETURN_URL'),
        'currency' => env('PESE_PAY_CURRENCY', 'USD'),
        'sandbox' => env('PESE_PAY_SANDBOX', false),
        'use_sdk' => env('PESE_PAY_USE_SDK', true),
    ],

];

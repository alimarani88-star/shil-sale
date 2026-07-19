<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'shiliran' => [
        'base_uri' => env('SHILIRAN_API_BASE', 'https://app.shiliran.ir'),
        'key'      => env('GLOBAL_API_KEY'),
        'cert'     => env('SHILIRAN_CERT',storage_path('certs/cacert.pem')),
    ],

    'bmi' => [
        'terminal_id' => env('BMI_TERMINAL_ID'),
        'merchant_id' => env('BMI_MERCHANT_ID'),
        'key' => env('BMI_KEY'),
        'callback_url' => env('BMI_CALLBACK_URL'),
        'verify_ssl' => env('BMI_VERIFY_SSL', true),
        'request_url' => env('BMI_REQUEST_URL', 'https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest'),
        'verify_url' => env('BMI_VERIFY_URL', 'https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify'),
        'purchase_url' => env('BMI_PURCHASE_URL', 'https://sadad.shaparak.ir/VPG/Purchase'),
    ],

];

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

    'wa_gateway' => [
        'driver' => env('WA_GATEWAY_DRIVER', 'gowa'), // options: 'gowa', 'wa_akg', 'log'
        'gowa' => [
            'base_url' => env('GOWA_BASE_URL', 'http://localhost:3000'),
            'user' => env('GOWA_BASIC_USER', ''),
            'pass' => env('GOWA_BASIC_PASS', ''),
            'device_id' => env('GOWA_DEVICE_ID', ''),
        ],
        'wa_akg' => [
            'base_url' => env('WA_AKG_BASE_URL', 'http://localhost:3001'),
            'api_key' => env('WA_AKG_API_KEY', ''),
            'session_id' => env('WA_AKG_SESSION_ID', 'default'),
        ],
    ],

];

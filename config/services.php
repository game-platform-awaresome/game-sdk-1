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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'wlc' => [
        'app_id' => env('WLC_APP_ID'),
        'app_secret' => env('WLC_APP_SECRET'),
        'authentication_check_url' => 'https://wlc.nppa.gov.cn/test/authentication/check',
        'authentication_query_url' => 'https://wlc.nppa.gov.cn/test/authentication/query',
        'collection_loginout_url' => 'https://wlc.nppa.gov.cn/test/collection/loginout/hWmg2t',
    ]

];

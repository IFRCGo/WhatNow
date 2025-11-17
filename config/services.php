<?php

return [




    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => null,
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => null,
    ],

    'google_analytics' => [
        'tracker_id' => env('GOOGLE_ANALYTICS_TRACKER_ID')
    ],

    'google_web_api_key' => env('GOOGLE_WEB_API_KEY'),

    'sentry_dsn' => env('SENTRY_DSN')
];

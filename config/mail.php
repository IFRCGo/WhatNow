<?php

return [




    'driver' => env('MAIL_DRIVER', 'smtp'),



    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),



    'port' => env('MAIL_PORT', 587),



    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],



    'encryption' => env('MAIL_ENCRYPTION', 'tls'),



    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),



    'sendmail' => '/usr/sbin/sendmail -bs',


    'markdown' => [
        'theme' => 'whatnow',

        'paths' => [
            resource_path('views/vendor/mail'),
            'timeout' => 80,
        ],
    ],

    'support_email' => 'im@ifrc.org',

    'endpoint_url' => env('MAIL_ENDPOINT') . '?apiKey=' . env('MAIL_API_KEY'),
    'mail_from' => env('MAIL_FROM'),

];

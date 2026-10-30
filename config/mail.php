<?php

return [
    'default' => env('MAIL_MAILER', env('MAIL_DRIVER', 'smtp')),
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME', env('MAIL_ENCRYPTION') === 'ssl' ? 'smtps' : 'smtp'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => 80,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        'sendmail' => ['transport' => 'sendmail', 'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i')],
        'log' => ['transport' => 'log'],
        'array' => ['transport' => 'array'],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
    'markdown' => [
        'theme' => 'whatnow',
        'paths' => [resource_path('views/vendor/mail')],
    ],
    'support_email' => 'im@ifrc.org',
    'endpoint_url' => env('MAIL_ENDPOINT') . '?apiKey=' . env('MAIL_API_KEY'),
    'mail_from' => env('MAIL_FROM'),
];

<?php

return [
    'cors_profile' => Spatie\Cors\CorsProfile\DefaultProfile::class,

    'default_profile' => [
        'allow_origins' => [
            '*',
        ],

        'allow_methods' => [
            'POST',
            'GET',
            'OPTIONS',
            'PUT',
            'PATCH',
            'DELETE',
        ],

        'allow_headers' => [
            'Content-Type',
            'X-Auth-Token',
            'Origin',
            'Authorization',
            'x-api-key',
        ],


        'max_age' => 60 * 60 * 24,
    ],
];

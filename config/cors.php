<?php

return [
    'paths' => ['*'],
    'allowed_methods' => ['POST', 'GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Auth-Token', 'Origin', 'Authorization', 'x-api-key'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,
];

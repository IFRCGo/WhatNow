<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => ['title' => 'WhatNow API'],
            'routes' => ['api' => 'admin/documentation'],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'swagger_ui_assets_path' => 'vendor/swagger-api/swagger-ui/dist/',
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => 'json',
                'annotations' => [app_path()],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => ['api' => [], 'asset' => [], 'docs' => [], 'oauth2_callback' => []],
            'group_options' => [],
        ],
        'paths' => [
            'docs' => public_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH'),
            'excludes' => [],
        ],
        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
            'open_api_spec_version' => '3.0.0',
        ],
        'securityDefinitions' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                    'description' => 'Enter token in format (Bearer <token>)',
                ],
            ],
            'security' => [['bearerAuth' => []]],
        ],
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy' => false,
        'proxy' => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT'),
        'validator_url' => null,
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('SWAGGER_URL', rtrim(env('APP_URL', 'http://localhost'), '/') . '/api'),
        ],
    ],
];

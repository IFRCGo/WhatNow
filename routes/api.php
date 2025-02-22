<?php

// Public API endpoints - no authentication required
Route::group(['middleware' => 'api'], function () {
    // Organisation and location related endpoints
    Route::get('organisations', 'WhatNow\OrganisationController@list'); // List all organizations
    Route::get('organisations/{countryCode}', 'WhatNow\OrganisationController@get'); // Get organization by country code
    Route::get('organisations/{countryCode}/alerts', 'Alert\AlertController@listByCountryCode'); // Get alerts for a specific country
    Route::get('organisations/{countryCode}/instructions', 'WhatNow\InstructionController@listByCountryCode'); // Get instructions for a country

    // Alert related endpoints
    Route::get('alerts/{identifier}', 'Alert\AlertController@getByIdentifier'); // Get specific alert by ID

    // Terms and conditions
    Route::get('terms/latest', 'Terms\TermsController@latest'); // Get latest terms version

    // Instructions and event types
    Route::get('instructions/{id}/{translation}/{stage}/download', 'WhatNow\InstructionController@renderImage'); // Download instruction image
    Route::get('event-types/', 'WhatNow\EventTypeController@list'); // List all event types
    Route::post('event-types/', 'WhatNow\EventTypeController@create'); // Create new event type

});

// Protected routes - requires API authentication
Route::group(['middleware' => 'auth:api'], function () {
    // Usage statistics and monitoring
    Route::get('usage/applications', 'WhatNow\UsageController@listApplicationRequestCount'); // Get app usage stats
    Route::get('usage/endpoints', 'WhatNow\UsageController@listEndpointRequestCount'); // Get endpoint usage stats
    Route::get('usage/export/applications', 'WhatNow\UsageController@exportApplicationUsageCsv'); // Export app usage as CSV
    Route::get('usage/export/endpoints', 'WhatNow\UsageController@exportEndpointUsageCsv'); // Export endpoint usage as CSV
    Route::get('usage/totals', 'WhatNow\UsageController@getTotals'); // Get total usage statistics

    // Authentication and user management
    Route::post('logout', 'Auth\LoginController@logout'); // Logout current user
    Route::patch('settings/password', 'Settings\PasswordController@update'); // Update password

    // User CRUD operations
    Route::get('users', 'Auth\UserController@list'); // List all users
    Route::get('users/admins', 'Auth\UserController@listAdmins'); // List admin users
    Route::get('users/me', 'Auth\UserController@me'); // Get current user info
    Route::get('users/{id}', 'Auth\UserController@view'); // View specific user
    Route::post('users', 'Auth\UserController@create'); // Create new user
    Route::patch('users/{id}', 'Auth\UserController@update'); // Update user
    Route::delete('users/{id}', 'Auth\UserController@delete'); // Delete user
    Route::get('users/{id}/resend', 'Auth\UserConfirmationController@sendConfirmationEmail'); // Resend confirmation email
    Route::post('users/{id}/reactivate', 'Auth\UserController@reactivate'); // Reactivate user
    Route::post('users/{id}/deactivate', 'Auth\UserController@deactivate'); // Deactivate user

    // Role management
    Route::get('roles', 'Auth\RoleController@listRoles'); // List all roles

    // Organization and instruction management
    Route::get('organisations/instructions/export', 'WhatNow\InstructionController@exportCsv'); // Export instructions
    Route::put('organisations/{countryCode}', 'WhatNow\OrganisationController@updateByCountryCode'); // Update org by country
    Route::get('organisations/{countryCode}/instructions/revisions/latest', 'WhatNow\InstructionController@listLatestDraftsByCountryCode'); // Get latest instruction drafts

    // Instructions CRUD and translation
    Route::get('instructions/{id}', 'WhatNow\InstructionController@view'); // View instruction
    Route::get('instructions/{id}/revisions/latest', 'WhatNow\InstructionController@viewLatestDraft'); // View latest draft
    Route::post('instructions/{id}/revisions/', 'WhatNow\InstructionController@createTranslation'); // Create translation
    Route::patch('instructions/{id}/revisions/{translationId}', 'WhatNow\InstructionController@publishTranslation'); // Publish translation
    Route::post('instructions', 'WhatNow\InstructionController@create'); // Create instruction
    Route::put('instructions/{id}', 'WhatNow\InstructionController@update'); // Update instruction
    Route::delete('instructions/{id}', 'WhatNow\InstructionController@delete'); // Delete instruction
    Route::post('instructions/{countryCode}/publish', 'WhatNow\InstructionController@publishTranslations'); // Publish translations

    // Application management
    Route::get('apps', 'WhatNow\ApplicationController@list'); // List all apps
    Route::post('apps', 'WhatNow\ApplicationController@create'); // Create new app
    Route::get('apps/{id}', 'WhatNow\ApplicationController@get'); // Get specific app
    Route::delete('apps/{id}', 'WhatNow\ApplicationController@delete'); // Delete app
    Route::patch('apps/{id}', 'WhatNow\ApplicationController@update'); // Update app

    // Audit and history
    Route::get('history', 'Audit\HistoryController@list'); // List all history
    Route::get('history/{id}', 'Audit\HistoryController@get'); // Get specific history
    Route::get('users/{id}/history', 'Audit\HistoryController@listForUser'); // Get user history

    // Import/Export functionality
    Route::post('import/{countryCode}/{languageCode}', 'WhatNow\WhatNowImportController@import'); // Import data
    Route::get('template/{country_code}/{language_code}', 'WhatNow\WhatNowImportController@export'); // Export template
    Route::get('template/{country_code}', 'WhatNow\WhatNowImportController@exportBlank'); // Export blank template

    // Terms management
    Route::get('terms', 'Terms\TermsController@list'); // List terms
    Route::get('terms/all', 'Terms\TermsController@listAll'); // List all terms versions
    Route::post('terms', 'Terms\TermsController@create'); // Create new terms

    // Region management
    Route::get('regions/{country_code}', 'WhatNow\RegionController@getAllForOrganisation'); // Get all regions for org
    Route::get('regions/{country_code}/{code}', 'WhatNow\RegionController@getForCountryCode'); // Get specific region
    Route::post('regions', 'WhatNow\RegionController@createRegion'); // Create region
    Route::put('regions/region/{regionId}', 'WhatNow\RegionController@updateRegion'); // Update region
    Route::delete('regions/region/{regionId}', 'WhatNow\RegionController@deleteRegion'); // Delete region

    //file upload
    Route::post('upload-file', 'FileController@uploadFile');

});

// Guest routes - for unauthenticated users
Route::group(['middleware' => 'guest:api'], function () {
    // Authentication routes
    Route::post('login', 'Auth\LoginController@login'); // Login
    Route::post('login/{driver}', 'Auth\OAuthController@login'); // OAuth login
    Route::post('register', 'Auth\RegisterController@register'); // Register new user
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail'); // Send password reset email
    Route::post('password/reset', 'Auth\ResetPasswordController@reset'); // Reset password

    // User confirmation
    Route::get('confirm/{token}', 'Auth\UserConfirmationController@confirm'); // Confirm user email
    Route::post('confirm/password/set', 'Auth\UserConfirmationController@storePassword'); // Set initial password
});

// Translation route - accessible to all
Route::get('translations/{locale}', 'TranslationController@show'); // Get translations for locale

Route::get('/health', function () {
    return response()->json(['status' => 'ok']); // Or a more detailed status
});

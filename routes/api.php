<?php



Route::group(['middleware' => 'api'], function () {
    Route::get('organisations', 'WhatNow\OrganisationController@list');
    Route::get('alerts/{identifier}', 'Alert\AlertController@getByIdentifier');
    Route::get('organisations/{countryCode}', 'WhatNow\OrganisationController@get');
    Route::get('organisations/{countryCode}/alerts', 'Alert\AlertController@listByCountryCode');
    Route::get('organisations/{countryCode}/instructions', 'WhatNow\InstructionController@listByCountryCode');
    Route::get('terms/latest', 'Terms\TermsController@latest');
    Route::get('instructions/{id}/{translation}/{stage}/download', 'WhatNow\InstructionController@renderImage');
    Route::get('event-types/', 'WhatNow\EventTypeController@list');
    Route::post('event-types/', 'WhatNow\EventTypeController@create');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('usage/applications', 'WhatNow\UsageController@listApplicationRequestCount');
    Route::get('usage/endpoints', 'WhatNow\UsageController@listEndpointRequestCount');
    Route::get('usage/export/applications', 'WhatNow\UsageController@exportApplicationUsageCsv');
    Route::get('usage/export/endpoints', 'WhatNow\UsageController@exportEndpointUsageCsv');
    Route::get('usage/totals', 'WhatNow\UsageController@getTotals');

    Route::post('logout', 'Auth\LoginController@logout');

    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::get('users', 'Auth\UserController@list');
    Route::get('users/admins', 'Auth\UserController@listAdmins');
    Route::get('users/me', 'Auth\UserController@me');     Route::get('users/{id}', 'Auth\UserController@view');
    Route::post('users', 'Auth\UserController@create');
    Route::patch('users/{id}', 'Auth\UserController@update');
    Route::delete('users/{id}', 'Auth\UserController@delete');
    Route::get('users/{id}/resend', 'Auth\UserConfirmationController@sendConfirmationEmail')->name('confirm.resend');
    Route::post('users/{id}/reactivate', 'Auth\UserController@reactivate');
    Route::post('users/{id}/deactivate', 'Auth\UserController@deactivate');

    Route::get('roles', 'Auth\RoleController@listRoles');

    Route::get('organisations/instructions/export', 'WhatNow\InstructionController@exportCsv');
    Route::put('organisations/{countryCode}', 'WhatNow\OrganisationController@updateByCountryCode');
    Route::get('organisations/{countryCode}/instructions/revisions/latest', 'WhatNow\InstructionController@listLatestDraftsByCountryCode');
    Route::get('instructions/{id}', 'WhatNow\InstructionController@view');
    Route::get('instructions/{id}/revisions/latest', 'WhatNow\InstructionController@viewLatestDraft');
    Route::post('instructions/{id}/revisions/', 'WhatNow\InstructionController@createTranslation');
    Route::patch('instructions/{id}/revisions/{translationId}', 'WhatNow\InstructionController@publishTranslation');
    Route::post('instructions', 'WhatNow\InstructionController@create');
    Route::put('instructions/{id}', 'WhatNow\InstructionController@update');
    Route::delete('instructions/{id}', 'WhatNow\InstructionController@delete');
    Route::post('instructions/{countryCode}/publish', 'WhatNow\InstructionController@publishTranslations');

    Route::get('apps', 'WhatNow\ApplicationController@list');
    Route::post('apps', 'WhatNow\ApplicationController@create');
    Route::get('apps/{id}', 'WhatNow\ApplicationController@get');
    Route::delete('apps/{id}', 'WhatNow\ApplicationController@delete');
    Route::patch('apps/{id}', 'WhatNow\ApplicationController@update');

    Route::get('history', 'Audit\HistoryController@list');
    Route::get('history/{id}', 'Audit\HistoryController@get');
    Route::get('users/{id}/history', 'Audit\HistoryController@listForUser');

    Route::post('import/{countryCode}/{languageCode}', 'WhatNow\WhatNowImportController@import');

    Route::get('template/{country_code}/{language_code}', 'WhatNow\WhatNowImportController@export')->name('import.template.download');
    Route::get('template/{country_code}', 'WhatNow\WhatNowImportController@exportBlank')->name('import.template.blank.download');

    Route::get('terms', 'Terms\TermsController@list');
    Route::get('terms/all', 'Terms\TermsController@listAll');
    Route::post('terms', 'Terms\TermsController@create');

    Route::get('regions/{country_code}', 'WhatNow\RegionController@getAllForOrganisation');
    Route::get('regions/{country_code}/{code}', 'WhatNow\RegionController@getForCountryCode');
    Route::post('regions', 'WhatNow\RegionController@createRegion');
    Route::put('regions/region/{regionId}', 'WhatNow\RegionController@updateRegion');
    Route::delete('regions/region/{regionId}', 'WhatNow\RegionController@deleteRegion');

                });

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('login/{driver}', 'Auth\OAuthController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

        Route::get('confirm/{token}', 'Auth\UserConfirmationController@confirm')->name('confirm');
    Route::post('confirm/password/set', 'Auth\UserConfirmationController@storePassword')->name('password.store');
});

Route::get('translations/{locale}', 'TranslationController@show');

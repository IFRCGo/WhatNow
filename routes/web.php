<?php

// OAuth authentication routes
Route::get('oauth/{driver}', 'Auth\OAuthController@redirectToProvider')
    ->name('oauth');  // Initiates OAuth flow by redirecting to the specified provider (e.g., Google, Facebook)

Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')
    ->name('oauth.callback');  // Handles the OAuth provider's response and completes authentication

// Password management routes
Route::get('password/reset/{token}', function () {
    return view('index');
})->name('password.reset');  // Displays password reset form for users with a valid reset token

Route::get('password/set/{token}', function () {
    return view('index');
})->name('password.set');  // Shows password setup form for new users or after reset request

// Authentication routes
Route::get('login', function () {
    return view('index');
})->name('login');  // Displays the main login page

Route::get('login/confirmed', function () {
    return view('index');
})->name('login.account_confirmed');  // Success page shown after account email verification

Route::get('login/confirm-failed', function () {
    return view('index');
})->name('login.confirmation_failed');  // Error page shown when email verification fails

// API documentation
Route::get('/endpoints', [\App\Http\Controllers\RouteController::class, 'index'])
    ->name('endpoints.index');  // Displays list of available API endpoints with documentation

// Single Page Application (SPA) support
Route::get('{path}', function () {
    return view('index');
})->where('path', '(.*)');  // Catches all other routes and returns to SPA frontend for client-side routing

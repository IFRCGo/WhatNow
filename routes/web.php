<?php



Route::get('oauth/{driver}', 'Auth\OAuthController@redirectToProvider')->name('oauth');
Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');

Route::get('password/reset/{token}', function () {
    return view('index');
})->name('password.reset');

Route::get('password/set/{token}', function () {
    return view('index');
})->name('password.set');

Route::get('login', function () {
    return view('index');
})->name('login');

Route::get('login/confirmed', function () {
    return view('index');
})->name('login.account_confirmed');

Route::get('login/confirm-failed', function () {
    return view('index');
})->name('login.confirmation_failed');

Route::get('{path}', function () {
    return view('index');
})->where('path', '(.*)');

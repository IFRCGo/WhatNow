<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;



$factory->define(App\Models\Access\User\User::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'password_updated_at' => new Carbon('now'),
        'remember_token' => str_random(10),
        'confirmation_code' => \App\Models\Access\User\UserConfirmationToken::generate(),
        'confirmed' => 1
    ];
});

$factory->state(App\Models\Access\User\User::class, 'unconfirmed', function (Faker $faker) {
    return [
        'confirmed' => 0
    ];
});

$factory->state(App\Models\Access\User\User::class, 'no-password', function (Faker $faker) {
    return [
        'password_updated_at' => null
    ];
});

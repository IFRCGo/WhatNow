<?php

$factory->define(App\Models\Access\User\UserProfile::class, function (Faker\Generator $faker) {
    return [
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName
    ];
});

$factory->state(App\Models\Access\User\UserProfile::class, 'notifications_off', function (Faker\Generator $faker) {
    return [
        'notifications_enabled' => false,
    ];
});

<?php

$factory->define(App\Models\Access\User\UserOrganisation::class, function (Faker\Generator $faker) {
    return [
        'organisation_code' => $faker->randomElement(['NZL', 'TUN', 'CHE', 'ESP', 'NOR'])
    ];
});

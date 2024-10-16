<?php


$factory->define(\App\Models\Terms::class, function (Faker\Generator $faker) {
    return [
        'version' => $faker->unique()->randomFloat(2),
        'content' => $faker->realText(),
        'created_at' => $faker->dateTimeThisYear,
        'user_id' => 2
    ];
});

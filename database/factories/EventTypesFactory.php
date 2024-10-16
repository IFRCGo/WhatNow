<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;



$factory->define(App\Models\EventType::class, function (Faker $faker) {
    return [
        'name' => str_random(10),
        'icon' => str_random(10),
        'code' => str_random(10),
    ];
});


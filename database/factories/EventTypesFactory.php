<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventTypesFactory extends Factory
{
    protected $model = \App\Models\EventType::class;

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'name' => \Illuminate\Support\Str::random(10),
            'icon' => \Illuminate\Support\Str::random(10),
            'code' => \Illuminate\Support\Str::random(10),
        ];
    }
}

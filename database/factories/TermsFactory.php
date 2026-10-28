<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TermsFactory extends Factory
{
    protected $model = \App\Models\Terms::class;

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'version' => $faker->unique()->randomFloat(2),
            'content' => $faker->realText(),
            'created_at' => $faker->dateTimeThisYear,
            'user_id' => 2
        ];
    }
}

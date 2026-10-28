<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserProfileFactory extends Factory
{
    protected $model = \App\Models\Access\User\UserProfile::class;

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName
        ];
    }

    public function notificationsOff(): static
    {
        return $this->state(fn () => [
            'notifications_enabled' => false,
        ]);
    }
}

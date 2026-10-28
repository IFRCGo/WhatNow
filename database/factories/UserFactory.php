<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserFactory extends Factory
{
    protected $model = \App\Models\Access\User\User::class;

    public function definition(): array
    {
        $faker = $this->faker;
        static $password;

        return [
            'email' => $faker->unique()->safeEmail,
            'password' => $password ?: $password = bcrypt('secret'),
            'password_updated_at' => new Carbon('now'),
            'remember_token' => \Illuminate\Support\Str::random(10),
            'confirmation_code' => \App\Models\Access\User\UserConfirmationToken::generate(),
            'confirmed' => 1
        ];
    }

    public function unconfirmed(): static
    {
        return $this->state(fn () => [
            'confirmed' => 0
        ]);
    }

    public function noPassword(): static
    {
        return $this->state(fn () => [
            'password_updated_at' => null
        ]);
    }
}

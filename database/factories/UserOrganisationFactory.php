<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserOrganisationFactory extends Factory
{
    protected $model = \App\Models\Access\User\UserOrganisation::class;

    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'organisation_code' => $faker->randomElement(['NZL', 'TUN', 'CHE', 'ESP', 'NOR'])
        ];
    }
}

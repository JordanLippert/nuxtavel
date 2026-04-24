<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'email'      => $this->faker->unique()->safeEmail(),
            'birth_date' => $this->faker->date('Y-m-d', '2000-01-01'),
            'password'   => Hash::make('password'),
            'avatar'     => null,
        ];
    }
}

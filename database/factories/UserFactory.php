<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'f_name' => fake()->firstName(),
            'l_name' => fake()->lastName(),

            'email' => fake()->unique()->safeEmail(),
            'phone_no' => fake()->phoneNumber(),

            'email_verified_at' => now(),

            'password' => static::$password ??= Hash::make('password'),

            'user_type' => 'company', // 🔥 important (NOT NULL)
            'is_active' => 1,
            'profile_completed' => 1,

            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

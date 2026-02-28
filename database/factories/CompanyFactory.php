<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'company_name' => $this->faker->company(),

            'sub_domain' => Str::slug($this->faker->unique()->company()),

            'company_house_number' => $this->faker->buildingNumber(),
            'company_mobile' => $this->faker->phoneNumber(),
            'company_email' => $this->faker->unique()->safeEmail(),

            'business_type' => $this->faker->randomElement(['Ltd', 'LLC', 'Sole']),

            'street' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'address' => $this->faker->address(),
            'postcode' => $this->faker->postcode(),
            'country' => $this->faker->country(),

            'billing_plan_id' => null,

            'subscription_status' => 'active',
            'payment_failed_count' => 0,
            'payment_status' => 'pending',

            'subscription_start' => now()->subMonth(),
            'subscription_end' => now()->subDay(),

            'trial_ends_at' => null,
            'status' => 'Active',
        ];
    }
}

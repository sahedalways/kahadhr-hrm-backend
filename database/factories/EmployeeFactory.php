<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [

            'company_id' => null,
            'user_id' => null,

            // basic info
            'f_name' => fake()->firstName(),
            'l_name' => fake()->lastName(),
            'title' => 'Mr',

            'email' => fake()->unique()->safeEmail(),
            'phone_no' => fake()->phoneNumber(),

            'avatar' => null,
            'is_active' => 1,
            'role' => 'employee',

            'nationality' => 'British',
            'share_code' => null,
            'share_code_status' => 'unavailable',

            'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),

            'job_title' => 'Staff',

            'department_id' => null,
            'team_id' => null,

            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',

            'start_date' => now()->subMonths(2),
            'end_date' => null,

            'invite_token' => Str::random(20),
            'invite_token_expires_at' => now()->addDays(7),

            'verified' => 1,

            'billable_from' => now()->subMonth(),
        ];
    }
}

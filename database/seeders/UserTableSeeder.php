<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
  public function run(): void
  {
    // --------------------------
    // 1. Super Admin
    // --------------------------
    User::create([
      'f_name' => 'Super',
      'l_name' => 'Admin',
      'email' => 'admin@admin.com',
      'phone_no' => '0177xxxxxxx',
      'profile_completed' => true,
      'password' => Hash::make('12345678'),
      'user_type' => 'superAdmin',
      'email_verified_at' => now(),
      'phone_verified_at' => now(),
    ]);

    // --------------------------
    // 2. Companies
    // --------------------------
    $companyUser1 = User::create([
      'f_name' => 'XYZ LTD',
      'l_name' => 'Company',
      'email' => 'company@company.com',
      'phone_no' => '016165238944',
      'password' => Hash::make('12345678'),
      'user_type' => 'company',
      'email_verified_at' => now(),
      'phone_verified_at' => now(),
    ]);

    $company1 = Company::create([
      'user_id' => $companyUser1->id,
      'company_name' => 'XYZ IT Solutions Ltd.',
      'company_house_number' => 'House 12, Road 8',
      'company_mobile' => '016165238944',
      'company_email' => 'company@company.com',
      'subscription_status' => 'trial',
      'trial_ends_at' => now()->addDays(14),
      'subscription_start' => now(),
      'subscription_end' => now()->addDays(14),
    ]);



    $companyUser2 = User::create([
      'f_name' => 'ABC Tech Ltd',
      'l_name' => 'Company',
      'email' => 'abc@company.com',
      'phone_no' => '01712345678',
      'password' => Hash::make('12345678'),
      'user_type' => 'company',
      'email_verified_at' => now(),
      'phone_verified_at' => now(),
    ]);

    $company2 = Company::create([
      'user_id' => $companyUser2->id,
      'company_name' => 'ABC Tech Solutions Ltd.',
      'company_house_number' => 'House 34, Road 12',
      'company_mobile' => '01712345678',
      'company_email' => 'abc@company.com',
      'subscription_status' => 'trial',
      'subscription_start' => now(),
      'subscription_end' => now()->addDays(14),
      'trial_ends_at' => now()->addDays(14),
    ]);



    // --------------------------
    // 3. Employees
    // --------------------------
    $employeesCompany1 = [
      ['f_name' => 'John', 'l_name' => 'Doe', 'email' => 'emp@abc.com', 'phone_no' => '01710000001'],
      ['f_name' => 'Jane', 'l_name' => 'Smith', 'email' => 'jane@abc.com', 'phone_no' => '01710000002'],
    ];

    $employeesCompany2 = [
      ['f_name' => 'Alice', 'l_name' => 'Brown', 'email' => 'emp@xyz.com', 'phone_no' => '01720000001'],
      ['f_name' => 'Bob', 'l_name' => 'Johnson', 'email' => 'jane@xyz.com', 'phone_no' => '01720000002'],
    ];

    $employeeModels1 = [];
    foreach ($employeesCompany1 as $emp) {
      $user = User::create([
        'f_name' => $emp['f_name'],
        'l_name' => $emp['l_name'],
        'email' => $emp['email'],
        'phone_no' => $emp['phone_no'],
        'password' => Hash::make('12345678'),
        'user_type' => 'employee',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
      ]);

      $employeeModels1[] = Employee::create([
        'user_id' => $user->id,
        'company_id' => $company1->id,
        'role' => 'employee',
        'email' => $emp['email'],
        'nationality' => 'Bangladeshi',
        'date_of_birth' => now()->subYears(24),
        'f_name' => $emp['f_name'],   // Added
        'l_name' => $emp['l_name'],   // Added
        'contract_hours' => 0,
        'salary_type' => 'monthly',
        'billable_from' => now()->addDays(3),
        'start_date' => now(),
      ]);
    }

    $employeeModels2 = [];
    foreach ($employeesCompany2 as $emp) {
      $user = User::create([
        'f_name' => $emp['f_name'],
        'l_name' => $emp['l_name'],
        'email' => $emp['email'],
        'phone_no' => $emp['phone_no'],
        'password' => Hash::make('12345678'),
        'user_type' => 'employee',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
      ]);

      $employeeModels2[] = Employee::create([
        'user_id' => $user->id,
        'company_id' => $company2->id,
        'role' => 'employee',
        'email' => $emp['email'],
        'nationality' => 'Bangladeshi',
        'date_of_birth' => now()->subYears(30),
        'f_name' => $emp['f_name'],   // Added
        'l_name' => $emp['l_name'],   // Added
        'contract_hours' => 50,
        'salary_type' => 'hourly',
        'billable_from' => now()->addDays(3),
        'start_date' => now(),
      ]);
    }


    // --------------------------
    // 4. Departments
    // --------------------------
    $departments1 = [];
    foreach (['IT', 'HR', 'Marketing'] as $deptName) {
      $departments1[] = Department::create([
        'company_id' => $company1->id,
        'name' => $deptName,
      ]);
    }

    $departments2 = [];
    foreach (['Development', 'Support', 'Sales'] as $deptName) {
      $departments2[] = Department::create([
        'company_id' => $company2->id,
        'name' => $deptName,
      ]);
    }


    // --------------------------
    // 5. Teams
    // --------------------------
    // $teams1 = [];
    // foreach ($departments1 as $dept) {
    //   $teams1[] = Team::create([
    //     'company_id' => $company1->id,
    //     'department_id' => $dept->id,
    //     'name' => $dept->name . ' Team 1',
    //   ]);
    //   $teams1[] = Team::create([
    //     'company_id' => $company1->id,
    //     'department_id' => $dept->id,
    //     'name' => $dept->name . ' Team 2',
    //   ]);
    // }

    // $teams2 = [];
    // foreach ($departments2 as $dept) {
    //   $teams2[] = Team::create([
    //     'company_id' => $company2->id,
    //     'department_id' => $dept->id,
    //     'name' => $dept->name . ' Team 1',
    //   ]);
    //   $teams2[] = Team::create([
    //     'company_id' => $company2->id,
    //     'department_id' => $dept->id,
    //     'name' => $dept->name . ' Team 2',
    //   ]);
    // }
  }
}

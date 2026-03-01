<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeeBillableStatus extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();


        if (!Schema::hasColumn('employees', 'is_billable')) {
            $this->markTestSkipped('is_billable column does not exist in employees table.');
        }
    }


    /** @test */
    public function it_updates_employees_who_are_due_to_become_billable_today()
    {
        $this->withoutExceptionHandling();

        Log::info('Starting test: it_updates_employees_who_are_due_to_become_billable_today');

        // একটি কোম্পানি তৈরি
        $company = Company::factory()->create();

        // 1. ✅ আজকে billable_from - আপডেট হবে
        $employee1 = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Due',
            'l_name' => 'Today',
            'email' => 'due.today@example.com',
            'title' => 'Mr',
            'phone_no' => '01711111111',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::today(),
            'is_billable' => false,
        ]);

        // 2. ✅ গতকাল থেকে বিলযোগ্য - আপডেট হবে
        $employee2 = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Due',
            'l_name' => 'Yesterday',
            'email' => 'due.yesterday@example.com',
            'title' => 'Mr',
            'phone_no' => '01722222222',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::yesterday(),
            'is_billable' => false,
        ]);

        // 3. ❌ আগামীকাল থেকে বিলযোগ্য - আপডেট হবে না
        $employee3 = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Due',
            'l_name' => 'Tomorrow',
            'email' => 'due.tomorrow@example.com',
            'title' => 'Mr',
            'phone_no' => '01733333333',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::tomorrow(),
            'is_billable' => false,
        ]);

        // 4. ✅ ইতিমধ্যে বিলযোগ্য true - আপডেট হবে না (কিন্তু true-ই থাকবে)
        $employee4 = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Already',
            'l_name' => 'Billable',
            'email' => 'already.billable@example.com',
            'title' => 'Mr',
            'phone_no' => '01744444444',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::yesterday(),
            'is_billable' => true,
        ]);

        // 5. ❌ billable_from null - আপডেট হবে না
        $employee5 = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'No',
            'l_name' => 'Billable Date',
            'email' => 'no.date@example.com',
            'title' => 'Mr',
            'phone_no' => '01755555555',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => null,
            'is_billable' => false,
        ]);

        // ডিবাগ: কমান্ড রানের আগে ডাটা
        Log::info('Before command:', [
            'employee1_is_billable' => $employee1->is_billable,
            'employee2_is_billable' => $employee2->is_billable,
            'employee3_is_billable' => $employee3->is_billable,
            'employee4_is_billable' => $employee4->is_billable,
            'employee5_is_billable' => $employee5->is_billable,
        ]);

        // কমান্ড রান
        Artisan::call('employees:update-billable');
        $output = Artisan::output();

        Log::info('Command output', ['output' => $output]);

        // ডাটাবেস রিফ্রেশ
        $employee1->refresh();
        $employee2->refresh();
        $employee3->refresh();
        $employee4->refresh();
        $employee5->refresh();

        // ডিবাগ: কমান্ড রানের পর ডাটা
        Log::info('After command:', [
            'employee1_is_billable' => $employee1->is_billable,
            'employee2_is_billable' => $employee2->is_billable,
            'employee3_is_billable' => $employee3->is_billable,
            'employee4_is_billable' => $employee4->is_billable,
            'employee5_is_billable' => $employee5->is_billable,
        ]);

        // ✅ Assertions - boolean চেক
        $this->assertTrue($employee1->is_billable, 'Employee with billable_from today should be billable');
        $this->assertTrue($employee2->is_billable, 'Employee with billable_from yesterday should be billable');
        $this->assertFalse($employee3->is_billable, 'Employee with billable_from tomorrow should not be billable');
        $this->assertTrue($employee4->is_billable, 'Employee already billable should remain billable');
        $this->assertFalse($employee5->is_billable, 'Employee with null billable_from should not be billable');

        // ✅ Count check - আপডেট হওয়া সংখ্যা
        $this->assertStringContainsString('Updated 2 employees to billable.', $output);

        Log::info('Test completed successfully');
    }

    /** @test */
    public function it_does_not_update_deleted_employees()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();

        // ডিলিট করা এমপ্লয়ী (আপডেট হবে না)
        $deletedEmployee = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Deleted',
            'l_name' => 'Employee',
            'email' => 'deleted@example.com',
            'title' => 'Mr',
            'phone_no' => '01766666666',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::yesterday(),
            'is_billable' => false,
            'deleted_at' => now(),
        ]);

        // নরমাল এমপ্লয়ী (আপডেট হবে)
        $normalEmployee = Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Normal',
            'l_name' => 'Employee',
            'email' => 'normal@example.com',
            'title' => 'Mr',
            'phone_no' => '01777777777',
            'is_active' => 1,
            'role' => 'employee',
            'nationality' => 'British',
            'date_of_birth' => '1990-01-01',
            'job_title' => 'Staff',
            'contract_hours' => 40,
            'salary_type' => 'hourly',
            'employment_status' => 'full-time',
            'start_date' => now()->subMonths(2),
            'verified' => 1,
            'billable_from' => Carbon::yesterday(),
            'is_billable' => false,
            'deleted_at' => null,
        ]);

        Artisan::call('employees:update-billable');
        $output = Artisan::output();

        $deletedEmployee->refresh();
        $normalEmployee->refresh();

        $this->assertFalse($deletedEmployee->is_billable, 'Deleted employee should not be updated');
        $this->assertTrue($normalEmployee->is_billable, 'Normal employee should be updated');
        $this->assertStringContainsString('Updated 1 employees to billable.', $output);
    }
}

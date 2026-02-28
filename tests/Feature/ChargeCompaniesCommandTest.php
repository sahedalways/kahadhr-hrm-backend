<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\CompanyChargeRate;
use App\Models\Employee;
use App\Models\User;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;

class ChargeCompaniesCommandTest extends TestCase
{


    /** @test */
    public function it_handles_payment_failure_correctly()
    {
        $this->withoutExceptionHandling();

        Log::info('Starting test: it_handles_payment_failure_correctly');

        // 1. রেট তৈরি
        CompanyChargeRate::create(['rate' => 15.00]);

        // 2. কোম্পানি তৈরি
        $company = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Test Company',
            'sub_domain' => 'test-company-' . uniqid(),
            'company_email' => 'test@example.com',
            'subscription_status' => 'active',
            'subscription_end' => Carbon::yesterday(),
            'payment_status' => 'pending',
            'payment_failed_count' => 0,
            'status' => 'Active',
            'company_house_number' => '123',
            'company_mobile' => '1234567890',
            'business_type' => 'Ltd',
            'street' => 'Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'address' => 'Test Address',
            'postcode' => '12345',
            'country' => 'UK',
        ]);

        // 3. এমপ্লয়ী তৈরি
        for ($i = 0; $i < 5; $i++) {
            Employee::create([
                'company_id' => $company->id,
                'user_id' => User::factory()->create()->id,
                'f_name' => 'Test',
                'l_name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@example.com',
                'title' => 'Mr',
                'phone_no' => '1234567' . $i . '0',
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
                'billable_from' => now()->subDays(5),
            ]);
        }

        // 4. ব্যাংক তথ্য তৈরি (কার্ড থাকলেও payment fail হবে)
        $company->bankInfos()->create([
            'stripe_payment_method_id' => 'pm_test_12345',
        ]);

        // 5. Mock Gateway - Failed Response
        $gatewayMock = Mockery::mock(\App\Services\PaymentGateway::class);
        $gatewayMock->shouldReceive('charge')
            ->once()
            ->andReturn((object)[
                'success' => false,
                'message' => 'Payment failed',
                'status' => 'failed',
            ]);

        $this->app->instance(\App\Services\PaymentGateway::class, $gatewayMock);
        Log::info('Payment gateway mocked for failure');

        // 6. কমান্ড রান
        Artisan::call('companies:charge');
        $output = Artisan::output();
        Log::info('Command output', ['output' => $output]);

        // 7. ডাটাবেজ চেক
        $company->refresh();

        Log::info('Company after failure:', [
            'payment_status' => $company->payment_status,
            'payment_failed_count' => $company->payment_failed_count,
            'subscription_status' => $company->subscription_status,
        ]);

        // 8. Assertions
        $this->assertEquals('failed', $company->payment_status);
        $this->assertEquals(1, $company->payment_failed_count);
        $this->assertEquals('active', $company->subscription_status); // Still active

        // 9. Invoice check - Payment failed হলে invoice থাকবে না
        $this->assertDatabaseMissing('invoices', [
            'company_id' => $company->id,
        ]);

        Log::info('Test completed successfully');
    }

    /** @test */
    public function it_suspends_company_after_three_failures()
    {
        $this->withoutExceptionHandling();

        Log::info('Starting test: it_suspends_company_after_three_failures');

        // কোম্পানি তৈরি (already 2 failed attempts)
        $company = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Test Company',
            'sub_domain' => 'test-company-' . uniqid(),
            'company_email' => 'test@example.com',
            'subscription_status' => 'active',
            'subscription_end' => Carbon::yesterday(),
            'payment_status' => 'failed',
            'payment_failed_count' => 2, // Already 2 failures
            'status' => 'Active',
            'company_house_number' => '123',
            'company_mobile' => '1234567890',
            'business_type' => 'Ltd',
            'street' => 'Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'address' => 'Test Address',
            'postcode' => '12345',
            'country' => 'UK',
        ]);

        // 1 employee তৈরি
        Employee::create([
            'company_id' => $company->id,
            'user_id' => User::factory()->create()->id,
            'f_name' => 'Test',
            'l_name' => 'Employee',
            'email' => 'employee@example.com',
            'title' => 'Mr',
            'phone_no' => '1234567890',
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
            'billable_from' => now()->subDays(5),
        ]);

        // Bank info
        $company->bankInfos()->create([
            'stripe_payment_method_id' => 'pm_test_12345',
        ]);

        // Mock Gateway - Failed again
        $gatewayMock = Mockery::mock(\App\Services\PaymentGateway::class);
        $gatewayMock->shouldReceive('charge')
            ->once()
            ->andReturn((object)[
                'success' => false,
                'message' => 'Payment failed',
                'status' => 'failed',
            ]);

        $this->app->instance(\App\Services\PaymentGateway::class, $gatewayMock);

        // কমান্ড রান
        Artisan::call('companies:charge');

        // Check company suspended
        $company->refresh();

        $this->assertEquals('failed', $company->payment_status);
        $this->assertEquals(3, $company->payment_failed_count);
        $this->assertEquals('suspended', $company->subscription_status);

        Log::info('Suspension test completed');
    }
}

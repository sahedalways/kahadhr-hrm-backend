<?php

namespace Tests\Feature;

use App\Facades\PaymentGateway;
use Tests\TestCase;
use App\Models\Company;
use App\Models\CompanyChargeRate;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;

class ChargeCompaniesCommandTest extends TestCase
{


    /** @test */
    public function it_charges_company_successfully()
    {

        $this->withoutExceptionHandling();


        Log::info('Starting test: it_charges_company_successfully');

        Notification::fake();


        $rate = CompanyChargeRate::create(['rate' => 15.00]);
        $this->assertNotNull($rate, 'Company charge rate created');
        Log::info('Company charge rate created', ['rate' => $rate->rate]);

        $company = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Test Company',
            'sub_domain' => 'test-company-' . uniqid(),
            'company_email' => 'test@example.com',
            'subscription_status' => 'active',
            'subscription_end' => Carbon::yesterday(),
            'payment_status' => 'pending',
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

        Log::info('Company created with ID: ' . $company->id);


        $employees = [];
        for ($i = 0; $i < 5; $i++) {
            $employee = Employee::create([
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
            $employees[] = $employee;
        }

        Log::info('Employees created:', [
            'count' => count($employees),
            'first_employee_billable_from' => $employees[0]->billable_from->toDateTimeString()
        ]);


        $bankInfo = $company->bankInfos()->create([
            'stripe_payment_method_id' => 'pm_test_12345',
        ]);

        Log::info('Bank info created', [
            'bank_info_id' => $bankInfo->id,
            'payment_method' => $bankInfo->stripe_payment_method_id
        ]);


        $defaultCard = $company->fresh()->defaultCard();

        Log::info('Default card check:', [
            'exists' => $defaultCard ? 'yes' : 'no',
            'payment_method' => $defaultCard?->stripe_payment_method_id,
            'company_id' => $defaultCard?->company_id
        ]);


        $this->assertNotNull($defaultCard, 'Default card should exist');
        $this->assertEquals('pm_test_12345', $defaultCard->stripe_payment_method_id);


        $gatewayMock = Mockery::mock(\App\Services\PaymentGateway::class);
        $gatewayMock->shouldReceive('charge')
            ->once()
            ->andReturn((object)[
                'success' => true,
                'transaction_id' => 'test_txn_123',
                'status' => 'succeeded',
            ]);

        $this->app->instance(\App\Services\PaymentGateway::class, $gatewayMock);
        Log::info('Payment gateway mocked');

        // কমান্ড রান করুন
        Log::info('Running companies:charge command');
        $exitCode = Artisan::call('companies:charge');


        $output = Artisan::output();
        Log::info('Command output', ['output' => $output]);
        $this->assertEquals(0, $exitCode, 'Command executed successfully');


        $company->refresh();
        Log::info('Company after refresh', [
            'payment_status' => $company->payment_status,
            'payment_failed_count' => $company->payment_failed_count,
            'subscription_status' => $company->subscription_status,
        ]);


        $this->assertEquals('paid', $company->payment_status);
        $this->assertEquals(0, $company->payment_failed_count);

        $this->assertDatabaseHas('invoices', [
            'company_id' => $company->id,
            'status' => 'paid',
        ]);

        Log::info('Test completed successfully');
    }
}

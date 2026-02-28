<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Mockery;

class ActiveCompanyFromTrialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // যে কোন সেটআপ কোড
    }

    /** @test */
    public function it_activates_companies_whose_trial_has_ended()
    {
        $this->withoutExceptionHandling();

        Log::info('Starting test: it_activates_companies_whose_trial_has_ended');

        // 1. ট্রায়াল পিরিয়ড শেষ হয়ে গেছে (গতকাল)
        $company1 = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Trial Ended Company 1',
            'sub_domain' => 'trial-ended-1-' . uniqid(),
            'company_email' => 'trial1@example.com',
            'subscription_status' => 'trial',
            'trial_ends_at' => Carbon::yesterday(), // গতকাল শেষ
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

        // 2. আরেকটি কোম্পানি যার ট্রায়াল আজ শেষ হচ্ছে
        $company2 = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Trial Ends Today',
            'sub_domain' => 'trial-today-' . uniqid(),
            'company_email' => 'trial2@example.com',
            'subscription_status' => 'trial',
            'trial_ends_at' => Carbon::today(), // আজ শেষ
            'payment_status' => 'pending',
            'status' => 'Active',
            'company_house_number' => '456',
            'company_mobile' => '0987654321',
            'business_type' => 'LLC',
            'street' => 'Another St',
            'city' => 'Another City',
            'state' => 'Another State',
            'address' => 'Another Address',
            'postcode' => '67890',
            'country' => 'USA',
        ]);

        // 3. এই কোম্পানির ট্রায়াল এখনও চলছে (আগামীকাল শেষ হবে) - আপডেট হবে না
        $company3 = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Trial Not Ended',
            'sub_domain' => 'trial-not-ended-' . uniqid(),
            'company_email' => 'trial3@example.com',
            'subscription_status' => 'trial',
            'trial_ends_at' => Carbon::tomorrow(), // আগামীকাল শেষ
            'payment_status' => 'pending',
            'status' => 'Active',
            'company_house_number' => '789',
            'company_mobile' => '1122334455',
            'business_type' => 'Sole',
            'street' => 'Third St',
            'city' => 'Third City',
            'state' => 'Third State',
            'address' => 'Third Address',
            'postcode' => '13579',
            'country' => 'Canada',
        ]);

        Log::info('Test companies created', [
            'company1_trial_ends' => $company1->trial_ends_at->toDateString(),
            'company2_trial_ends' => $company2->trial_ends_at->toDateString(),
            'company3_trial_ends' => $company3->trial_ends_at->toDateString(),
        ]);

        // 4. কমান্ড রান করুন
        Artisan::call('app:active-company-from-trial');
        $output = Artisan::output();

        Log::info('Command output', ['output' => $output]);

        // 5. ডাটাবেস রিফ্রেশ করে কোম্পানিগুলোর নতুন অবস্থা চেক করুন
        $company1->refresh();
        $company2->refresh();
        $company3->refresh();

        // 6. Assertions - company1 (গতকাল শেষ) আপডেট হওয়া উচিত
        $this->assertEquals('active', $company1->subscription_status);
        $this->assertEquals(Carbon::today()->toDateString(), $company1->subscription_start->toDateString());
        $this->assertEquals(Carbon::today()->addMonth()->toDateString(), $company1->subscription_end->toDateString());
        $this->assertEquals('unpaid', $company1->payment_status);

        // 7. Assertions - company2 (আজ শেষ) আপডেট হওয়া উচিত
        $this->assertEquals('active', $company2->subscription_status);
        $this->assertEquals(Carbon::today()->toDateString(), $company2->subscription_start->toDateString());
        $this->assertEquals(Carbon::today()->addMonth()->toDateString(), $company2->subscription_end->toDateString());
        $this->assertEquals('unpaid', $company2->payment_status);

        // 8. Assertions - company3 (আগামীকাল শেষ) আপডেট হওয়া উচিত নয়
        $this->assertEquals('trial', $company3->subscription_status);
        $this->assertNull($company3->subscription_start);
        $this->assertNull($company3->subscription_end);
        $this->assertEquals('pending', $company3->payment_status);

        Log::info('Test completed successfully');
    }

    /** @test */
    public function it_does_not_activate_companies_with_other_statuses()
    {
        $this->withoutExceptionHandling();

        // 1. active status এর কোম্পানি (আপডেট হবে না)
        $activeCompany = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Active Company',
            'sub_domain' => 'active-' . uniqid(),
            'company_email' => 'active@example.com',
            'subscription_status' => 'active', // already active
            'trial_ends_at' => Carbon::yesterday(),
            'payment_status' => 'paid',
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

        // 2. suspended status এর কোম্পানি (আপডেট হবে না)
        $suspendedCompany = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Suspended Company',
            'sub_domain' => 'suspended-' . uniqid(),
            'company_email' => 'suspended@example.com',
            'subscription_status' => 'suspended',
            'trial_ends_at' => Carbon::yesterday(),
            'payment_status' => 'failed',
            'status' => 'Inactive',
            'company_house_number' => '456',
            'company_mobile' => '0987654321',
            'business_type' => 'LLC',
            'street' => 'Another St',
            'city' => 'Another City',
            'state' => 'Another State',
            'address' => 'Another Address',
            'postcode' => '67890',
            'country' => 'USA',
        ]);

        // 3. কমান্ড রান
        Artisan::call('app:active-company-from-trial');

        // 4. রিফ্রেশ
        $activeCompany->refresh();
        $suspendedCompany->refresh();

        // 5. Assertions - কোন পরিবর্তন হয়নি
        $this->assertEquals('active', $activeCompany->subscription_status);
        $this->assertEquals('suspended', $suspendedCompany->subscription_status);

        Log::info('Test completed: it_does_not_activate_companies_with_other_statuses');
    }

    /** @test */
    public function it_handles_empty_result_gracefully()
    {
        $this->withoutExceptionHandling();

        // কোন trial company নেই

        // কমান্ড রান
        $exitCode = Artisan::call('app:active-company-from-trial');
        $output = Artisan::output();

        // Assert command executed successfully
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('Checking trial companies...', $output);
        $this->assertStringContainsString('Trial activation completed.', $output);

        Log::info('Test completed: it_handles_empty_result_gracefully');
    }

    /** @test */
    public function it_activates_companies_with_trial_ended_yesterday_and_today()
    {
        $this->withoutExceptionHandling();

        // বিভিন্ন তারিখের ট্রায়াল শেষের কোম্পানি তৈরি
        $dates = [
            Carbon::yesterday(),
            Carbon::today(),
            Carbon::tomorrow(),
            Carbon::now()->subDays(5),
            Carbon::now()->addDays(5),
        ];

        foreach ($dates as $index => $date) {
            Company::create([
                'user_id' => User::factory()->create()->id,
                'company_name' => 'Company ' . $index,
                'sub_domain' => 'company-' . $index . '-' . uniqid(),
                'company_email' => 'company' . $index . '@example.com',
                'subscription_status' => 'trial',
                'trial_ends_at' => $date,
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
        }

        // কমান্ড রান
        Artisan::call('app:active-company-from-trial');

        // চেক করুন কতগুলো কোম্পানি active হয়েছে
        $activeCompanies = Company::where('subscription_status', 'active')->count();
        $trialCompanies = Company::where('subscription_status', 'trial')->count();

        // yesterday এবং today মিলে ২টি কোম্পানি active হওয়া উচিত
        $this->assertEquals(2, $activeCompanies);
        $this->assertEquals(3, $trialCompanies); // বাকি ৩টি trial-ই থাকবে

        Log::info('Test completed: it_activates_companies_with_trial_ended_yesterday_and_today');
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\SendOtpSmsForVerifyJob;
use App\Models\User;
use App\Models\Company;
use App\Models\SmsSetting;
use App\Services\API\VerificationService as APIVerificationService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OtpVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_completes_full_otp_sending_flow()
    {
        // Arrange
        Queue::fake();

        $phone = '+1234567890';



        $company = Company::create([
            'user_id' => User::factory()->create()->id,
            'company_name' => 'Trial Ended Company 1',
            'sub_domain' => 'trial-ended-1-' . uniqid(),
            'company_email' => 'trial1@example.com',
            'subscription_status' => 'trial',
            'trial_ends_at' => Carbon::yesterday(),
            'payment_status' => 'pending',
            'status' => 'Active',
            'company_house_number' => '123',
            'company_mobile' => $phone,
            'business_type' => 'Ltd',
            'street' => 'Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'address' => 'Test Address',
            'postcode' => '12345',
            'country' => 'UK',
        ]);

        // Create SMS settings
        SmsSetting::factory()->create([
            'twilio_sid' => 'test_sid',
            'twilio_auth_token' => 'test_token',
            'twilio_from' => '+1987654321'
        ]);

        // Act
        $response = $this->postJson('/api/send-otp', [
            'phone' => $phone,
            'company_name' => $company->company_name
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        Queue::assertPushed(SendOtpSmsForVerifyJob::class, function ($job) use ($phone) {
            return $job->phone === $phone && strlen($job->otp) === 6;
        });
    }

    /** @test */
    public function it_handles_otp_verification()
    {
        // Arrange
        $phone = '+1234567890';
        $otp = '123456';

        $repository = app(APIVerificationService::class);
        $repository->updateOrInsert([
            'phone' => $phone,
            'otp' => $otp,
        ]);

        // Act
        $response = $this->postJson('/api/verify-otp', [
            'phone' => $phone,
            'otp' => $otp
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['verified' => true]);
    }

    /** @test */
    public function it_rejects_invalid_otp()
    {
        // Arrange
        $phone = '+1234567890';

        $repository = app(APIVerificationService::class);
        $repository->updateOrInsert([
            'phone' => $phone,
            'otp' => '123456',
        ]);

        // Act
        $response = $this->postJson('/api/verify-otp', [
            'phone' => $phone,
            'otp' => '654321'
        ]);

        // Assert
        $response->assertStatus(400);
        $response->assertJson(['verified' => false]);
    }
}

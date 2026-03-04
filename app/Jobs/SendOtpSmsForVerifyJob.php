<?php

namespace App\Jobs;

use App\Models\SmsSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class SendOtpSmsForVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phone;
    protected string $otp;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phone, string $otp)
    {
        $this->phone = $phone;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = SmsSetting::first();

        if (!$settings) {
            \Log::error("No SMS settings found. Cannot send OTP.");
            return;
        }


        if (
            empty($settings->twilio_sid) ||
            empty($settings->twilio_auth_token) ||
            empty($settings->twilio_from)
        ) {
            \Log::error("Twilio credentials missing.");
            return;
        }

        try {
            $siteName = siteSetting()->site_title ?? 'Kahadhr HRM';

            $messageBody = "Use {$this->otp} as ONE-TIME verification of your phone number. " .
                "Your safety is in your hands. Never share this OTP. - {$siteName}";

            $client = new Client(
                (string) $settings->twilio_sid,
                (string) $settings->twilio_auth_token
            );

            $client->messages->create($this->phone, [
                'from' => $settings->twilio_from,
                'body' => $messageBody
            ]);

            \Log::info("OTP sent to {$this->phone}: {$this->otp}");
        } catch (\Throwable $e) {
            \Log::error("Failed to send OTP to {$this->phone}: " . $e->getMessage());
        }
    }
}

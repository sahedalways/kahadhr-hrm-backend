<?php

namespace App\Jobs;

use App\Models\SmsSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class SendOtpSmsJob implements ShouldQueue
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
            \Log::error("Twilio credentials missing", [
                'sid' => $settings->twilio_sid,
                'token' => $settings->twilio_auth_token,
                'from' => $settings->twilio_from,
            ]);
            return;
        }

        if (!preg_match('/^\+\d{10,15}$/', $this->phone)) {
            \Log::error("Invalid phone format: {$this->phone}");
            return;
        }

        try {
            $siteName = siteSetting()->site_title ?? 'KahadHR';
            $messageBody = "Use {$this->otp} as ONE-TIME KEY. Your safety is in your hands. Never share this OTP. - {$siteName}";

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

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
            empty($settings->account_sid) ||
            empty($settings->auth_token) ||
            empty($settings->from_number)
        ) {
            \Log::error("Twilio credentials missing", [
                'sid' => $settings->account_sid,
                'token' => $settings->auth_token,
                'from' => $settings->from_number,
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
                (string) $settings->account_sid,
                (string) $settings->auth_token
            );

            $client->messages->create($this->phone, [
                'from' => $settings->from_number,
                'body' => $messageBody
            ]);

            \Log::info("OTP sent to {$this->phone}: {$this->otp}");
        } catch (\Throwable $e) {
            \Log::error("Failed to send OTP to {$this->phone}: " . $e->getMessage());
        }
    }
}

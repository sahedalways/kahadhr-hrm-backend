<?php


namespace App\Jobs;

use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SendOtpEmailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $email;
  protected $otp;
  protected $name;


  /**
   * Create a new job instance.
   */
  public function __construct(string $email, $otp, ?string $name = null)
  {
    $this->email = $email;
    $this->otp = $otp;
    $this->name = $name;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $settings = EmailSetting::first();

    if (!$settings) {
      \Log::error("No Email settings found. Cannot send OTP.");
      return;
    }

    // Dynamic mail config
    Config::set('mail.mailers.smtp.transport', 'smtp');
    Config::set('mail.mailers.smtp.host', $settings->mail_host);
    Config::set('mail.mailers.smtp.port', $settings->mail_port);
    Config::set('mail.mailers.smtp.username', $settings->mail_username);
    Config::set('mail.mailers.smtp.password', $settings->mail_password);
    Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption);

    Config::set('mail.from.address', $settings->mail_from_address);
    Config::set('mail.from.name', $settings->mail_from_name);

    app()->forgetInstance('mailer');
    app()->forgetInstance('mail.manager');
    Mail::flushMacros();

    try {
      Mail::send('mail.email_verification', [
        'data' => [
          'companyName' => $this->name,
          'otp'      => $this->otp,
          'email'    => $this->email,
          'title'    => "Your Email Verification Code",
          'body'     => "Your email verification code is: {$this->otp}. It will expire in 2 minutes.",
        ]
      ], function ($message) {
        $message->to($this->email)
          ->subject('Your Email Verification Code');
      });

      \Log::info("OTP email sent to {$this->email}");
    } catch (\Exception $e) {
      \Log::error("Failed to send OTP email: " . $e->getMessage());
    }
  }
}

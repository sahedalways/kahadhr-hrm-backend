<?php


namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


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

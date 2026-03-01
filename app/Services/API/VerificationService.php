<?php

namespace App\Services\API;

use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsForVerifyJob;
use App\Repositories\API\VerificationRepository;
use Carbon\Carbon;

class VerificationService
{
  protected VerificationRepository $repository;

  public function __construct(VerificationRepository $repository)
  {
    $this->repository = $repository;
  }

  public function sendEmailOtp(?string $email = null,  ?string $name = null): bool
  {
    $otp = rand(100000, 999999);
    // $otp = 123456;

    $this->repository->updateOrInsert([
      'email' => $email,
      'otp' => $otp,
    ]);

    if ($email) {
      SendOtpEmailJob::dispatch($email, $otp, $name)->onConnection('sync')->onQueue('urgent');
    }


    return true;
  }



  public function sendPhoneOtp(string $phoneNo,  ?string $name = null): bool
  {
    $otp = rand(100000, 999999);
    // $otp = 123456;

    $this->repository->updateOrInsert([
      'phone' => $phoneNo,
      'otp' => $otp,
    ]);


    if ($phoneNo) {
      SendOtpSmsForVerifyJob::dispatch($phoneNo, $otp)->onConnection('sync')->onQueue('urgent');
    }

    return true;
  }







  public function verifyOtp(string $emailOrPhone, string $otp): bool
  {
    $record = $this->repository->findValidOtp($emailOrPhone, $otp);


    if (!$record) {
      throw new \Exception('Invalid OTP!');
    }

    if (Carbon::parse($record->created_at)->diffInMinutes(now()) > 2) {
      throw new \Exception('OTP expired!');
    }

    $this->repository->deleteOtp($record);

    return true;
  }
}

<?php

namespace App\Services\API;

use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendOtpSmsForVerifyJob;
use App\Repositories\API\VerificationRepository;


class VerificationService
{
  protected VerificationRepository $repository;

  public function __construct(VerificationRepository $repository)
  {
    $this->repository = $repository;
  }

  public function sendEmailOtp(?string $email = null,  string $companyName): bool
  {
    // $otp = rand(100000, 999999);
    $otp = 123456;

    $this->repository->updateOrInsert([
      'email' => $email,
      'otp' => $otp,
    ]);

    if ($email) {
      SendOtpEmailJob::dispatch($email, $otp, $companyName)->onConnection('sync')->onQueue('urgent');
    }

    // if ($phone) {
    //   // SendOtpSmsForVerifyJob::dispatch($phone, $otp)->onConnection('sync')->onQueue('urgent');
    // }

    return true;
  }

  public function verifyOtp(string $emailOrPhone, string $otp): bool
  {
    $record = $this->repository->findValidOtp($emailOrPhone, $otp);
    if (!$record) return false;

    $this->repository->deleteOtp($record);

    return true;
  }
}

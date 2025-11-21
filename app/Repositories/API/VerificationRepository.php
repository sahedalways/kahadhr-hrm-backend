<?php

namespace App\Repositories\API;

use App\Models\EmailVerification;
use App\Models\OtpVerification;
use Carbon\Carbon;

class VerificationRepository
{
  public function updateOrInsert(array $data): OtpVerification
  {
    // Delete previous OTPs for same email or phone
    OtpVerification::where('email', $data['email'] ?? null)
      ->orWhere('phone', $data['phone'] ?? null)
      ->delete();

    return OtpVerification::create([
      'email' => $data['email'] ?? null,
      'phone' => $data['phone'] ?? null,
      'otp' => $data['otp'],
      'expires_at' => now()->addMinutes(2),
    ]);
  }

  public function findValidOtp(string $emailOrPhone, string $otp): ?OtpVerification
  {
    return OtpVerification::where(function ($q) use ($emailOrPhone) {
      $q->where('email', $emailOrPhone)
        ->orWhere('phone', $emailOrPhone);
    })
      ->where('otp', $otp)
      ->where('expires_at', '>', now())
      ->first();
  }

  public function deleteOtp(OtpVerification $otp): void
  {
    $otp->delete();
  }
}

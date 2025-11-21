<?php

namespace App\Services;

use App\Models\User;


class AuthService
{

  public function loginAdmin(string $email, string $password)
  {
    $user = User::where('email', $email)
      ->where('user_type', 'superAdmin')
      ->first();

    if (!$user) {
      return false;
    }

    if (!password_verify($password, $user->password)) {
      return false;
    }

    return $user;
  }



  public function loginCompany(string $email, string $password)
  {
    $user = User::where('email', $email)
      ->where('user_type', 'company')
      ->first();

    if (!$user) {
      return false;
    }

    if (!password_verify($password, $user->password)) {
      return false;
    }

    return $user;
  }



  public function sendOtpSms($phone, $otp)
  {
    // Example: Twilio or other SMS provider code here
    // SmsService::send($phone, "Your login OTP is: $otp");

    \Log::info("OTP Sent to {$phone}: {$otp}");
  }
}

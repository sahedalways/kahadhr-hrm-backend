<?php

namespace App\Services;

use App\Jobs\SendOtpSmsJob;
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
    // Find the user with the given email and user_type 'company'
    $user = User::where('email', $email)
      ->where('user_type', 'company')
      ->first();

    if (!$user) {
      return false;
    }


    if (!password_verify($password, $user->password)) {
      return false;
    }


    if (!$user->company || $user->company->status !== 'Active') {
      return false;
    }

    return $user;
  }




  public function loginEmployee(string $email, string $password)
  {
    // Find the user with the given email and user_type 'company'
    $user = User::with('employee', 'employee.company')->where('email', $email)
      ->where('user_type', 'employee')
      ->first();

    if (!$user) {
      return false;
    }


    if (!password_verify($password, $user->password)) {
      return false;
    }


    $employee = $user->employee()->withoutGlobalScopes()->first();

    if (!$employee) {
      return false;
    }

    $company = $employee->company()->withoutGlobalScopes()->first();

    if (!$company || $company->status !== 'Active') {
      return false;
    }


    return $user;
  }



  public function sendOtpSms($phone, $otp)
  {
    // Example: Twilio or other SMS provider code here
    // SendOtpSmsJob::dispatch($phone, $otp)->onConnection('sync')->onQueue('urgent');

    \Log::info("OTP Sent to {$phone}: {$otp}");
  }
}

<?php

namespace App\Repositories;

use App\Services\AuthService;


class AuthRepository
{


  protected $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }


  public function loginAdmin(string $email, string $password)
  {
    return $this->authService->loginAdmin($email, $password);
  }


  public function sendOtpSms($phone, $otp)
  {
    $this->authService->sendOtpSms($phone, $otp);
  }
}

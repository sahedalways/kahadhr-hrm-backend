<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
  /**
   * Admin login logic
   *
   * @param string $email
   * @param string $password
   * @return bool
   */
  public function loginAdmin(string $email, string $password): bool
  {
    return Auth::attempt([
      'email'     => $email,
      'password'  => $password,
      'user_type' => 'admin',
    ]);
  }
}

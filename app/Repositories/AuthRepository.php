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

  /**
   * Attempt login for admin
   *
   * @param string $email
   * @param string $password
   * @return bool
   */
  public function loginAdmin(string $email, string $password): bool
  {
    return $this->authService->loginAdmin($email, $password);
  }
}

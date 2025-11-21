<?php

namespace App\Services\API;

use App\Models\User;
use App\Repositories\API\FrontAuthRepository;
use Illuminate\Support\Facades\Hash;

class FrontAuthService
{

  protected FrontAuthRepository $authRepo;

  public function __construct(FrontAuthRepository $authRepo)
  {
    $this->authRepo = $authRepo;
  }

  public function registerCompany(array $data): User
  {
    // Hash Password
    $data['password'] = Hash::make($data['password']);

    return $this->authRepo->createCompanyRegister($data);
  }
}

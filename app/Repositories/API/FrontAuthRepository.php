<?php

namespace App\Repositories\API;

use App\Models\Company;
use App\Models\CompanyBankInfo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FrontAuthRepository
{

  public function createCompanyRegister(array $data): User
  {
    return DB::transaction(function () use ($data) {

      // 1️⃣ Create User
      $user = User::create([
        'f_name'   => $data['company_name'],
        'l_name'   => 'Company',
        'email'    => $data['company_email'],
        'phone_no' => $data['company_mobile'],
        'password' => $data['password'],
        'user_type' => 'company',
        'phone_verified_at'  => now(),
        'email_verified_at'  => now(),
      ]);

      // 2️⃣ Create Company
      Company::create([
        'user_id'              => $user->id,
        'company_name'         => $data['company_name'],
        'company_house_number' => $data['company_house_number'],
        'company_mobile'       => $data['company_mobile'],
        'company_email'        => $data['company_email'],
        'subscription_start'   => now(),
        'subscription_end'     => now()->addDays(14),
        'subscription_status'  => 'trial',
      ]);



      return $user;
    });
  }
}

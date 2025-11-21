<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyBankInfo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    /**
     * ============================
     *  Create Super Admin User
     * ============================
     */
    User::create([
      'f_name'            => 'Super',
      'l_name'            => 'Admin',
      'email'             => 'admin@admin.com',
      'phone_no'          => '0177xxxxxxx',
      'password'          => Hash::make('12345678'),
      'user_type'         => 'superAdmin',
      'email_verified_at' => Carbon::now(),
      'phone_verified_at' => Carbon::now(),
    ]);


    /**
     * ============================
     *  Create Company User
     * ============================
     */
    $companyUser = User::create([
      'f_name'            => 'XYZ',
      'l_name'            => 'LTD.',
      'email'             => 'company@company.com',
      'phone_no'          => '016165238944',
      'password'          => Hash::make('12345678'),
      'user_type'         => 'company',
      'email_verified_at' => Carbon::now(),
      'phone_verified_at' => Carbon::now(),
    ]);


    /**
     * ============================
     *  Create Company (for the above user)
     * ============================
     */
    $company = Company::create([
      'user_id'               => $companyUser->id,
      'company_name'          => 'XYZ IT Solutions Ltd.',
      'company_house_number'  => 'House 12, Road 8',
      'company_mobile'        => '016165238944',
      'company_email'         => 'info@xyz.com',
    ]);


    /**
     * ============================
     *  Create Company Bank Info
     * ============================
     */


    CompanyBankInfo::create([
      'company_id'  => $company->id,
      'bank_name'   => 'BRAC Bank',
      'card_number' => '5500000000000004',
      'expiry_date' => '11/28',
      'cvv'         => '321',
    ]);
  }
}

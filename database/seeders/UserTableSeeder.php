<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    // for creating admin user
    User::create(attributes: [
      'f_name' => 'Super',
      'l_name' => 'Admin',
      'email' => 'super@admin.com',
      'phone_no' => '0177xxxxxxx',
      'password' => 12345678,
      'user_type' => 'superAdmin',
      'email_verified_at' => Carbon::now(),
    ]);


    // for creating regular company
    User::create([
      'f_name' => 'XYZ',
      'l_name' => 'LTD.',
      'email' => 'company@company.com',
      'phone_no' => '016165238944',
      'password' => 12345678,
      'user_type' => 'company',
      'email_verified_at' => Carbon::now(),
    ]);
  }
}
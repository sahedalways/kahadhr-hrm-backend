<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveBalancesSeeder extends Seeder
{
  public function run()
  {
    $now = Carbon::now();

    $data = [
      [
        'user_id' => 4,
        'company_id' => 1,
        'total_leave_in_liew' => 0.00,
        'used_leave_in_liew' => 0.00,
        'total_annual_hours' => 224.00,
        'used_annual_hours' => 0.00,
        'carry_over_hours' => 224.00,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'user_id' => 5,
        'company_id' => 1,
        'total_leave_in_liew' => 0.00,
        'used_leave_in_liew' => 0.00,
        'total_annual_hours' => 224.00,
        'used_annual_hours' => 0.00,
        'carry_over_hours' => 224.00,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'user_id' => 6,
        'company_id' => 2,
        'total_leave_in_liew' => 0.00,
        'used_leave_in_liew' => 0.00,
        'total_annual_hours' => 314.00,
        'used_annual_hours' => 0.00,
        'carry_over_hours' => 314.00,
        'created_at' => $now,
        'updated_at' => $now,
      ],
      [
        'user_id' => 7,
        'company_id' => 2,
        'total_leave_in_liew' => 0.00,
        'used_leave_in_liew' => 0.00,
        'total_annual_hours' => 314.00,
        'used_annual_hours' => 0.00,
        'carry_over_hours' => 314.00,
        'created_at' => $now,
        'updated_at' => $now,
      ],
    ];

    DB::table('leave_balances')->insert($data);
  }
}

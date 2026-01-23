<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_charge_rates')->insert([
            'rate' => 5.85,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

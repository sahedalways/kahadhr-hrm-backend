<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            UserTableSeeder::class,
            EmailSettingSeeder::class,
            SmsSettingSeeder::class,
            SocialSettingSeeder::class,
            CompanyChargeSeeder::class,
            LeaveTypeSeeder::class,
        ]);
    }
}

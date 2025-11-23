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
            UserTableSeeder::class,
            SiteSettingSeeder::class,
            EmailSettingSeeder::class,
            SmsSettingSeeder::class,
            SocialSettingSeeder::class,
            CompanyChargeSeeder::class,
        ]);
    }
}

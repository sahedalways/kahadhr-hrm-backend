<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
  public function run(): void
  {
    SiteSetting::create([
      'site_title'        => 'Kahadhr HRM',
      'logo'              => 'jpeg',
      'favicon'           => 'jpeg',
      'hero_image'        => 'webp',
      'site_phone_number' => '+8801877556633',
      'site_email'             => 'info@kahadhr.com',
      'copyright_text'    => '© ' . date('Y') . ' Kahadhr HRM. All rights reserved.',
    ]);
  }
}
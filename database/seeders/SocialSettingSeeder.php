<?php

namespace Database\Seeders;

use App\Models\SocialInfoSettings;
use Illuminate\Database\Seeder;

class SocialSettingSeeder extends Seeder
{
  public function run(): void
  {
    SocialInfoSettings::create([
      'facebook'  => 'https://facebook.com/Kahadhr-HRM',
      'twitter'   => 'https://twitter.com/Kahadhr-HRM',
      'instagram' => 'https://instagram.com/Kahadhr-HRM',
      'linkedin'  => 'https://linkedin.com/in/Kahadhr-HRM',
      'youtube'   => 'https://youtube.com/Kahadhr-HRM',
    ]);
  }
}
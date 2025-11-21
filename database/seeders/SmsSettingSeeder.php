<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmsSetting;

class SmsSettingSeeder extends Seeder
{
  public function run(): void
  {
    SmsSetting::create([
      'twilio_sid'        => 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
      'twilio_auth_token' => 'your_auth_token_here',
      'twilio_from'       => '+15005550006',
    ]);
  }
}

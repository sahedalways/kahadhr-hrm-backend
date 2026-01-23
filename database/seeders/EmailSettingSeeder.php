<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailSetting;

class EmailSettingSeeder extends Seeder
{
  public function run(): void
  {
    EmailSetting::create([
      'mail_mailer'      => 'smtp',
      'mail_host'        => 'sandbox.smtp.mailtrap.io',
      'mail_port'        => '2525',
      'mail_username'    => 'c390db9deb5326',
      'mail_password'    => '43f2ee0c767d61',
      'mail_encryption'  => 'tls',
      'mail_from_address' => 'no-reply@example.com',
      'mail_from_name'   => 'Kahadhr',
    ]);
  }
}

<?php

use App\Models\EmailSetting;

if (! function_exists('configureSmtp')) {
  /**
   * Configure SMTP dynamically for a given MailGateway instance.
   *
   * @param  EmailSetting  $gateway
   * @return void
   */
  function configureSmtp($gateway)
  {
    config([
      'mail.mailers.smtp.transport' => 'smtp',
      'mail.mailers.smtp.host' => $gateway->mail_host,
      'mail.mailers.smtp.port' => $gateway->mail_port,
      'mail.mailers.smtp.encryption' => $gateway->mail_encryption,
      'mail.mailers.smtp.username' => $gateway->mail_username,
      'mail.mailers.smtp.password' => $gateway->mail_password,
      'mail.from.address' => $gateway->mail_from_address,
      'mail.from.name' => $gateway->mail_from_name,
    ]);
  }
}

<?php

namespace App\Repositories;

use App\Models\EmailSetting;
use App\Models\SiteSetting;
use App\Models\SmsSetting;
use App\Models\SocialInfoSettings;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;


class SettingRepository

{
  /**
   * --------------------------
   * Get Settings
   * --------------------------
   */

  public function getSiteSettings(?int $companyId = null): SiteSetting
  {
    return SiteSetting::firstOrNew(['company_id' => $companyId]);
  }

  public function getMailSettings(?int $companyId = null): EmailSetting
  {
    return EmailSetting::firstOrNew(['company_id' => $companyId]);
  }

  public function getSmsSettings(?int $companyId = null): SmsSetting
  {
    return SmsSetting::firstOrNew(['company_id' => $companyId]);
  }

  public function getSocialSettings(?int $companyId = null): SocialInfoSettings
  {
    return SocialInfoSettings::firstOrNew(['company_id' => $companyId]);
  }


  /**
   * --------------------------
   * Save Settings
   * --------------------------
   */

  public function saveSiteSettings(array $data, ?int $companyId = null): SiteSetting
  {
    $settings = $this->getSiteSettings($companyId);
    $settings->company_id        = $companyId;
    $settings->site_title        = $data['site_title'] ?? $settings->site_title;
    $settings->site_phone_number = $data['site_phone_number'] ?? $settings->site_phone_number;
    $settings->site_email        = $data['site_email'] ?? $settings->site_email;
    $settings->copyright_text    = $data['copyright_text'] ?? $settings->copyright_text;

    // Handle logo, favicon, hero_image uploads
    if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
      $ext = $data['logo']->getClientOriginalExtension();
      $data['logo']->storeAs('image/settings', 'logo.' . $ext, 'public');
      $settings->logo = $ext;
    }

    if (isset($data['favicon']) && $data['favicon'] instanceof UploadedFile) {
      $ext = $data['favicon']->getClientOriginalExtension();
      $data['favicon']->storeAs('image/settings', 'favicon.' . $ext, 'public');
      $settings->favicon = $ext;
    }

    if (isset($data['hero_image']) && $data['hero_image'] instanceof UploadedFile) {
      $img = Image::read($data['hero_image']);
      $filename = 'hero.webp';
      $img->save(storage_path('app/public/image/settings/' . $filename));
      $settings->hero_image = 'webp';
    }

    $settings->save();

    // Cache
    cache()->forget("site_settings_{$companyId}");

    return $settings;
  }

  public function saveMailSettings(array $data, ?int $companyId = null): EmailSetting
  {
    $settings = $this->getMailSettings($companyId);
    $settings->company_id        = $companyId;
    $settings->mail_mailer       = $data['mail_mailer'] ?? $settings->mail_mailer;
    $settings->mail_host         = $data['mail_host'] ?? $settings->mail_host;
    $settings->mail_port         = $data['mail_port'] ?? $settings->mail_port;
    $settings->mail_username     = $data['mail_username'] ?? $settings->mail_username;
    $settings->mail_password     = $data['mail_password'] ?? $settings->mail_password;
    $settings->mail_encryption   = $data['mail_encryption'] ?? $settings->mail_encryption;
    $settings->mail_from_address = $data['mail_from_address'] ?? $settings->mail_from_address;
    $settings->mail_from_name    = $data['mail_from_name'] ?? $settings->mail_from_name;
    $settings->save();

    cache()->forget("mail_settings_{$companyId}");
    return $settings;
  }

  public function saveSmsSettings(array $data, ?int $companyId = null): SmsSetting
  {
    $settings = $this->getSmsSettings($companyId);
    $settings->company_id        = $companyId;
    $settings->twilio_sid        = $data['twilio_sid'] ?? $settings->twilio_sid;
    $settings->twilio_auth_token = $data['twilio_auth_token'] ?? $settings->twilio_auth_token;
    $settings->twilio_from       = $data['twilio_from'] ?? $settings->twilio_from;
    $settings->save();

    cache()->forget("sms_settings_{$companyId}");
    return $settings;
  }

  public function saveSocialSettings(array $data, ?int $companyId = null): SocialInfoSettings
  {
    $settings = $this->getSocialSettings($companyId);
    $settings->company_id = $companyId;
    $settings->facebook   = $data['facebook'] ?? $settings->facebook;
    $settings->twitter    = $data['twitter'] ?? $settings->twitter;
    $settings->instagram  = $data['instagram'] ?? $settings->instagram;
    $settings->linkedin   = $data['linkedin'] ?? $settings->linkedin;
    $settings->youtube    = $data['youtube'] ?? $settings->youtube;
    $settings->save();

    cache()->forget("social_settings_{$companyId}");
    return $settings;
  }
}

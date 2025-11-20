<?php

namespace App\Repositories;

use App\Models\ContactInfoSettings;
use App\Models\EmailSetting;
use App\Models\PaymentSetting;
use App\Models\SiteSetting;
use App\Models\SocialInfoSettings;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;


class SettingRepository
{
  /**
   * Get or create site settings (id = 1).
   *
   * @return \App\Models\SiteSetting
   */
  public function getSiteSettings(): SiteSetting
  {
    return SiteSetting::firstOrNew(['id' => 1]);
  }


  /**
   * Get or create payment settings (id = 1).
   *
   * @return \App\Models\PaymentSetting
   */
  public function getPaymentSettings(): PaymentSetting
  {
    return PaymentSetting::firstOrNew(['id' => 1]);
  }



  /**
   * Get or create mail settings (id = 1).
   *
   * @return \App\Models\EmailSetting
   */
  public function getMailSettings(): EmailSetting
  {
    return EmailSetting::firstOrNew(['id' => 1]);
  }




  /**
   * Get or create social settings (id = 1).
   *
   * @return \App\Models\SocialInfoSettings
   */
  public function getSocialSettings(): SocialInfoSettings
  {
    return SocialInfoSettings::firstOrNew(['id' => 1]);
  }



  /**
   * Get or create contact settings (id = 1).
   *
   * @return \App\Models\ContactInfoSettings
   */
  public function getContactInfoSettings(): ContactInfoSettings
  {
    return ContactInfoSettings::firstOrNew(['id' => 1]);
  }


  /**
   * Save or update site settings
   *
   * @param array $data
   * @return SiteSetting
   */
  public function saveSiteSettings(array $data): SiteSetting

  {
    $settings = $this->getSiteSettings();

    $settings->site_title        = $data['site_title'] ?? $settings->site_title;
    $settings->site_phone_number = $data['site_phone_number'] ?? $settings->site_phone_number;
    $settings->site_email        = $data['site_email'] ?? $settings->site_email;
    $settings->copyright_text    = $data['copyright_text'] ?? $settings->copyright_text;

    // Handle Logo
    if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
      $ext = $data['logo']->getClientOriginalExtension();
      $data['logo']->storeAs('image/settings', 'logo.' . $ext, 'public');
      $settings->logo = $ext;
    }

    // Handle Favicon
    if (isset($data['favicon']) && $data['favicon'] instanceof UploadedFile) {
      $ext = $data['favicon']->getClientOriginalExtension();
      $data['favicon']->storeAs('image/settings', 'favicon.' . $ext, 'public');
      $settings->favicon = $ext;
    }

    if (isset($data['hero_image']) && $data['hero_image'] instanceof UploadedFile) {
      $image = $data['hero_image'];

      $img = Image::read($image);

      $filename = 'hero.webp';
      $path = storage_path('app/public/image/settings/' . $filename);
      $img->save($path);

      $settings->hero_image = 'webp';
    }

    $settings->save();


    // Clear cache
    cache()->forget('site_settings');

    return $settings;
  }



  /**
   * Save or update mail settings
   *
   * @param array $data
   * @return EmailSetting
   */
  public function saveMailSettings(array $data): EmailSetting
  {
    $settings = $this->getMailSettings();

    $settings->mail_mailer       = $data['mail_mailer'] ?? null;
    $settings->mail_host         = $data['mail_host'] ?? null;
    $settings->mail_port         = $data['mail_port'] ?? null;
    $settings->mail_username     = $data['mail_username'] ?? null;
    $settings->mail_password     = $data['mail_password'] ?? null;
    $settings->mail_encryption   = $data['mail_encryption'] ?? null;
    $settings->mail_from_address = $data['mail_from_address'] ?? null;
    $settings->mail_from_name    = $data['mail_from_name'] ?? null;

    $settings->save();

    return $settings;
  }


  public function savePaymentSettings(array $data): PaymentSetting
  {
    $settings = $this->getPaymentSettings();

    $settings->gateway    = $data['gateway'] ?? 'bkash';
    $settings->app_key    = $data['app_key'] ?? null;
    $settings->app_secret = $data['app_secret'] ?? null;
    $settings->username   = $data['username'] ?? null;
    $settings->password   = $data['password'] ?? null;
    $settings->base_url   = $data['base_url'] ?? null;
    $settings->is_active  = $data['is_active'] ?? false;

    $settings->save();

    return $settings;
  }



  public function saveSocialSettings(array $data): SocialInfoSettings
  {
    $settings = $this->getSocialSettings();

    $settings->facebook  = $data['facebook']  ?? null;
    $settings->twitter   = $data['twitter']   ?? null;
    $settings->instagram = $data['instagram'] ?? null;
    $settings->linkedin  = $data['linkedin']  ?? null;
    $settings->youtube   = $data['youtube']   ?? null;

    $settings->save();

    return $settings;
  }




  public function saveContactInfoSettings(array $data): ContactInfoSettings
  {
    $settings = $this->getContactInfoSettings();

    $settings->email                 = $data['email']                  ?? null;
    $settings->phone                 = $data['phone']                  ?? null;
    $settings->dhaka_office_address  = $data['dhaka_office_address']   ?? null;
    $settings->gazipur_office_address = $data['gazipur_office_address'] ?? null;

    $settings->save();

    return $settings;
  }
}

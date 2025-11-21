<?php

namespace App\Services;

use App\Models\EmailSetting;
use App\Models\PaymentSetting;
use App\Models\SiteSetting;
use App\Models\SmsSetting;
use App\Models\SocialInfoSettings;
use App\Repositories\SettingRepository;


class SettingService
{
  protected $repository;

  public function __construct(SettingRepository $repository)
  {
    $this->repository = $repository;
  }


  /**
   * Save site settings
   *
   * @param array $data
   * @param int|null $companyId
   * @return SiteSetting
   */
  public function saveSiteSettings(array $data, ?int $companyId = null): SiteSetting
  {
    return $this->repository->saveSiteSettings($data, $companyId);
  }

  /**
   * Save mail settings
   *
   * @param array $data
   * @param int|null $companyId
   * @return EmailSetting
   */
  public function saveMailSettings(array $data, ?int $companyId = null): EmailSetting
  {
    return $this->repository->saveMailSettings($data, $companyId);
  }

  /**
   * Save SMS settings
   *
   * @param array $data
   * @param int|null $companyId
   * @return SmsSetting
   */
  public function saveSmsSettings(array $data, ?int $companyId = null): SmsSetting
  {
    return $this->repository->saveSmsSettings($data, $companyId);
  }

  /**
   * Save or update social settings
   *
   * @param array $data
   * @param int|null $companyId
   * @return SocialInfoSettings
   */
  public function saveSocialSettings(array $data, ?int $companyId = null): SocialInfoSettings
  {
    return $this->repository->saveSocialSettings($data, $companyId);
  }
}

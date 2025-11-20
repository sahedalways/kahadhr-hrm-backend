<?php

namespace App\Services;

use App\Models\EmailSetting;
use App\Models\PaymentSetting;
use App\Models\SiteSetting;
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
   * @return SiteSetting
   */
  public function saveSiteSettings(array $data): SiteSetting
  {

    return $this->repository->saveSiteSettings($data);
  }



  /**
   * Save mail settings
   *
   * @param array $data
   * @return EmailSetting
   */
  public function saveMailSettings(array $data): EmailSetting
  {

    return $this->repository->saveMailSettings($data);
  }


  /**
   * Save or update payment settings
   *
   * @param array $data
   * @return void
   */
  public function savePaymentSettings(array $data): void
  {
    $this->repository->savePaymentSettings($data);
  }

  /**
   * Save or update social settings
   *
   * @param array $data
   * @return void
   */
  public function saveSocialSettings(array $data): void
  {
    $this->repository->saveSocialSettings($data);
  }


  /**
   * Save or update contact info settings
   *
   * @param array $data
   * @return void
   */
  public function saveContactInfoSettings(array $data): void
  {
    $this->repository->saveContactInfoSettings($data);
  }
}

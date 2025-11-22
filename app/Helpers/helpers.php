<?php

use App\Models\SiteSetting;

if (!function_exists('siteSetting')) {
  function siteSetting()
  {

    $settings = SiteSetting::first();

    return $settings;
  }


  if (!function_exists('getSiteEmail')) {
    function getSiteEmail()
    {
      $settings = siteSetting();
      return $settings?->site_email ?? null;
    }
  }
}

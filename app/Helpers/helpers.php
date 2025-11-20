<?php

use App\Models\SiteSetting;

if (!function_exists('siteSetting')) {
  function siteSetting()
  {
    return cache()->remember('site_settings', 3600, function () {
      $settings = SiteSetting::first();

      return $settings;
    });
  }
}
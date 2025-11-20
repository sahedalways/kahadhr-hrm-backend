<?php

use App\Models\SiteSetting;

if (!function_exists('siteSetting')) {
  function siteSetting()
  {

    $settings = SiteSetting::first();

    return $settings;
  }
}
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


  if (!function_exists('statusBadge')) {
    /**
     * Returns HTML badge for status.
     *
     * @param string $status
     * @return string
     */
    function statusBadge($status)
    {
      if ($status === 'Active') {
        return '<span class="badge bg-success">Active</span>';
      } elseif ($status === 'Inactive') {
        return '<span class="badge bg-danger">Inactive</span>';
      } else {
        return '<span class="badge bg-secondary">Unknown</span>';
      }
    }
  }


  if (!function_exists('statusBadgeTwo')) {
    function statusBadgeTwo($status)
    {
      if ($status == 1) {
        return '<span class="badge bg-success">Active</span>';
      } else {
        return '<span class="badge bg-danger">Inactive</span>';
      }
    }
  }

  if (!function_exists('getCopyrightText')) {
    function getCopyrightText()
    {
      $settings = siteSetting();
      return $settings?->copyright_text ?? null;
    }
  }
}

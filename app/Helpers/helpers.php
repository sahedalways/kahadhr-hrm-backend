<?php

use App\Models\SiteSetting;

if (!function_exists('siteSetting')) {
  function siteSetting()
  {

    $settings = SiteSetting::first();

    return $settings;
  }
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
      return '<span class="badge bg-danger">Former</span>';
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

function currentCompanyId()
{
  $user = auth()->user();

  if (!$user) return null;

  if ($user->user_type === 'company') {
    return $user->company->id;
  }

  if ($user->user_type === 'employee') {
    return $user->employee->company_id;
  }

  return null;
}



class EnvUpdater
{
  /**
   * Update or add key in .env file
   */
  public static function set(array $data)
  {
    $envPath = base_path('.env');

    if (!file_exists($envPath)) return false;

    $env = file_get_contents($envPath);

    foreach ($data as $key => $value) {
      $pattern = "/^{$key}=.*/m";

      if (preg_match($pattern, $env)) {
        $env = preg_replace($pattern, "{$key}={$value}", $env);
      } else {
        $env .= "\n{$key}={$value}";
      }
    }

    file_put_contents($envPath, $env);

    return true;
  }
}

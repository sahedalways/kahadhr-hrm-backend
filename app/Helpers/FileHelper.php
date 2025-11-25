<?php

use App\Models\SiteSetting;

if (!function_exists('getFileUrl')) {
  function getFileUrl(?string $path, string $default = 'assets/img/default-image.jpg'): string
  {
    if (!$path) {
      return asset($default);
    }


    return asset('storage/' . ltrim($path, '/'));
  }


  if (!function_exists('getCompanyLogoUrl')) {
    /**
     * Get the logo URL for the current logged-in user's company.
     * Falls back to site settings logo or default image.
     *
     * @return string
     */
    function getCompanyLogoUrl(): string
    {

      $company = auth()->check() ? auth()->user()->company : null;


      if ($company && $company->company_logo && file_exists(storage_path('app/public/' . ltrim($company->company_logo, '/')))) {
        return asset('storage/' . ltrim($company->company_logo, '/'));
      }


      $siteSettings = SiteSetting::first();
      if ($siteSettings && $siteSettings->logo) {
        return getFileUrl('image/settings/logo.' . $siteSettings->logo);
      }


      return asset('assets/img/default-image.jpg');
    }
  }
}

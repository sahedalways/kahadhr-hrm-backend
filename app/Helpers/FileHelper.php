<?php

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

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
      $user = auth()->user();
      $company = null;

      if ($user) {
        if ($user->user_type === 'company') {
          $company = $user->company;
        } elseif ($user->user_type === 'employee' && $user->employee) {
          $company = $user->employee->company;
        }
      }

      // Check company logo
      if ($company && $company->company_logo && file_exists(storage_path('app/public/' . ltrim($company->company_logo, '/')))) {
        return asset('storage/' . ltrim($company->company_logo, '/'));
      }


      $siteSettings = SiteSetting::withoutGlobalScope('filterByUserType')->first();

      if ($siteSettings && $siteSettings->logo) {
        return getFileUrl('image/settings/logo.' . $siteSettings->logo);
      }


      return asset('assets/img/default-image.jpg');
    }
  }



  /**
   * Generic image upload helper
   *
   * @param UploadedFile|null $file
   * @param string $directory
   * @param string|null $oldFilePath
   * @return string|null
   */
  if (!function_exists('uploadImage')) {
    function uploadImage(?UploadedFile $file, string $directory, ?string $oldFilePath = null): ?string
    {
      if (!$file instanceof UploadedFile) {
        return $oldFilePath;
      }


      if ($oldFilePath && file_exists(storage_path('app/public/' . $oldFilePath))) {
        unlink(storage_path('app/public/' . $oldFilePath));
      }


      $storageDir = storage_path('app/public/' . $directory);
      if (!file_exists($storageDir)) {
        mkdir($storageDir, 0755, true);
      }


      $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();


      $img = Image::read($file->getRealPath());
      $path = $storageDir . '/' . $filename;
      $img->save($path);


      return $directory . '/' . $filename;
    }
  }


  if (!function_exists('shortFileName')) {
    /**
     * Shorten a file name while keeping the extension.
     *
     * @param string $name
     * @param int $length
     * @return string
     */
    function shortFileName(string $name, int $length = 15): string
    {
      $ext = pathinfo($name, PATHINFO_EXTENSION);
      $filenameWithoutExt = pathinfo($name, PATHINFO_FILENAME);

      if (strlen($filenameWithoutExt) <= $length) {
        return $name;
      }

      $start = substr($filenameWithoutExt, 0, 4);
      $end = substr($filenameWithoutExt, -4);

      return $start . '...' . $end . ($ext ? '.' . $ext : '');
    }
  }
}

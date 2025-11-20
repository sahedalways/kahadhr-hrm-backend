<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('getFileUrl')) {
  /**
   * Get full file URL from storage or return default.
   *
   * @param string|null $path   Path in storage/app/public
   * @param string $default     Path in public/ for default image
   * @return string
   */
  function getFileUrl(?string $path, string $default = 'assets/img/default-image.jpg'): string
  {
    if (!$path) {
      return asset($default);
    }


    if (Storage::disk('public')->exists($path)) {
      return Storage::url($path);
    }


    return asset($default);
  }
}
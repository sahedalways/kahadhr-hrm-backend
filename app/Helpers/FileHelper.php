<?php




if (!function_exists('getFileUrl')) {
  function getFileUrl(?string $path, string $default = 'assets/img/default-image.jpg'): string
  {
    if (!$path) {
      return asset($default);
    }


    return asset('storage/' . ltrim($path, '/'));
  }
}

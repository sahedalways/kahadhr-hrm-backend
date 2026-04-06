<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


// for clearing cache
Route::get('/clear', function () {
  Artisan::call('route:cache');
  Artisan::call('config:cache');
  Artisan::call('view:clear');
  Artisan::call('cache:clear');
  return 'Routes cache has been cleared';
});

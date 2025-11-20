<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

// for clearing cache
Route::get('/clear', function () {
  Artisan::call('route:cache');
  Artisan::call('config:cache');
  Artisan::call('view:clear');
  Artisan::call('cache:clear');
  return 'Routes cache has been cleared';
});

// for migrating db
Route::get('/migrate-db', function () {
  Artisan::call('migrate:refresh', [
    '--force' => true,
  ]);

  Artisan::call('db:seed', [
    '--force' => true,
  ]);

  return 'Migration refreshed and database seeded!';
});

// for linkup to storage
Route::get('/storage-link', function () {
  Artisan::call('storage:link');
  return 'Storage link created!';
});

// for test email
Route::get('/send-test-mail', function () {
  Mail::raw('This is a test email from Laravel.', function ($message) {
    $message->to('ssahed65@gmail.com')
      ->subject('Laravel Test Email');
  });

  return 'Test email sent to ssahed65@gmail.com';
});
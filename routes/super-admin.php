<?php

use App\Livewire\Backend\Dashboard;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\PasswordSettings;
use App\Livewire\Backend\Settings\PaymentSettings;
use App\Livewire\Backend\Settings\SiteSettings;
use App\Livewire\Backend\Settings\SocialSettings;
use Illuminate\Support\Facades\Route;



// for super admin
Route::group(['prefix' => 'super-admin', 'middleware' => ['auth', 'superAdmin'], 'as' => 'super-admin.'], function () {
  /* Admin Dashboard */
  Route::get('dashboard', Dashboard::class)->name('dashboard');

  // for settings group routes
  Route::prefix('/settings')->name('settings.')->group(function () {
    Route::get('/site', SiteSettings::class)->name('site');
    Route::get('/mail', MailSettings::class)->name('mail');
    Route::get('/payment', PaymentSettings::class)->name('payment');
    Route::get('/password', PasswordSettings::class)->name('password');
    Route::get('/social', SocialSettings::class)->name('social');
  });
});
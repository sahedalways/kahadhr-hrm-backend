<?php

use App\Livewire\Backend\Admin\Auth\AdminLogin;
use App\Livewire\Backend\Admin\Dashboard;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\PasswordSettings;
use App\Livewire\Backend\Settings\PaymentSettings;
use App\Livewire\Backend\Settings\SiteSettings;
use App\Livewire\Backend\Settings\SocialSettings;
use Illuminate\Support\Facades\Route;

// login route
Route::get('/', [AdminLogin::class, '__invoke'])->name('login');

// Super Admin routes under admin.demo.com
Route::domain('admin.' . config('app.base_domain'))->prefix('dashboard')->middleware(['auth', 'superAdmin'])->name('super-admin.')->group(function () {

  // Dashboard
  Route::get('/', Dashboard::class)->name('home');

  // Settings routes
  Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('site', SiteSettings::class)->name('site');
    Route::get('mail', MailSettings::class)->name('mail');
    Route::get('payment', PaymentSettings::class)->name('payment');
    Route::get('password', PasswordSettings::class)->name('password');
    Route::get('social', SocialSettings::class)->name('social');
  });
});

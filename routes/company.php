<?php


use App\Livewire\Backend\Company\Auth\CompanyLogin;
use App\Livewire\Backend\Company\Dashboard;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\PasswordSettings;
use App\Livewire\Backend\Settings\SiteSettings;
use App\Livewire\Backend\Settings\SmsSettings;
use App\Livewire\Backend\Settings\SocialSettings;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public routes for company (no auth required)
|--------------------------------------------------------------------------
*/

Route::domain('{company}.' . config('app.base_domain'))
  ->middleware(['guest', 'checkCompanySubdomain'])
  ->name('company.auth.')
  ->group(function () {
    Route::get('/', CompanyLogin::class)->name('login');
  });

/*
|--------------------------------------------------------------------------
| Authenticated company dashboard routes
|--------------------------------------------------------------------------
*/
Route::domain('{company}.' . config('app.base_domain'))
  ->prefix('dashboard')
  ->middleware(['auth', 'companyAdmin'])
  ->name('company.dashboard.')
  ->group(function () {
    // Dashboard home
    Route::get('/', Dashboard::class)->name('index');

    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
      Route::get('mail', MailSettings::class)->name('mail');
      Route::get('sms', SmsSettings::class)->name('sms');
      Route::get('password', PasswordSettings::class)->name('password');
      Route::get('social', SocialSettings::class)->name('social');
    });
  });

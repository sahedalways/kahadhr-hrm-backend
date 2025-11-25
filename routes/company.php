<?php


use App\Livewire\Backend\Company\Auth\CompanyLogin;
use App\Livewire\Backend\Company\Chat\ChatIndex;
use App\Livewire\Backend\Company\Dashboard;
use App\Livewire\Backend\Company\Documents\DocumentsIndex;
use App\Livewire\Backend\Company\Employees\UsersIndex;
use App\Livewire\Backend\Company\Leaves\LeavesIndex;
use App\Livewire\Backend\Company\Onboarding\OnboardingIndex;
use App\Livewire\Backend\Company\Reports\ReportsIndex;
use App\Livewire\Backend\Company\Schedule\ScheduleIndex;
use App\Livewire\Backend\Company\Settings\BankInfoSettings;
use App\Livewire\Backend\Company\Settings\ProfileSettings;
use App\Livewire\Backend\Company\Settings\VerificationCentreSettings;
use App\Livewire\Backend\Company\Timesheet\TimesheetIndex;
use App\Livewire\Backend\Company\Training\TrainingIndex;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\PasswordSettings;
use App\Livewire\Backend\Settings\SmsSettings;
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
      Route::get('profile', ProfileSettings::class)->name('profile');
      Route::get('bank-info', BankInfoSettings::class)->name('bank-info');
      Route::get('verification-center', VerificationCentreSettings::class)->name('verification-center');
    });


    Route::prefix('employees')->name('employees.')->group(function () {
      Route::get('/', UsersIndex::class)->name('index');
    });

    Route::prefix('chat')->name('chat.')->group(function () {
      Route::get('/', ChatIndex::class)->name('index');
    });

    Route::prefix('timesheet')->name('timesheet.')->group(function () {
      Route::get('/', TimesheetIndex::class)->name('index');
    });

    Route::prefix('schedule')->name('schedule.')->group(function () {
      Route::get('/', ScheduleIndex::class)->name('index');
    });

    Route::prefix('leaves')->name('leaves.')->group(function () {
      Route::get('/', LeavesIndex::class)->name('index');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
      Route::get('/', DocumentsIndex::class)->name('index');
    });

    Route::prefix('training')->name('training.')->group(function () {
      Route::get('/', TrainingIndex::class)->name('index');
    });

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
      Route::get('/', OnboardingIndex::class)->name('index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
      Route::get('/', ReportsIndex::class)->name('index');
    });
  });

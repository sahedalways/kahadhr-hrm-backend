<?php

use App\Http\Controllers\SetPasswordController;
use App\Livewire\Backend\Chat\ChatIndex;
use App\Livewire\Backend\Employee\Dashboard;
use App\Livewire\Backend\Employee\Auth\EmployeeLogin;
use App\Livewire\Backend\Employee\ClockIn\ClockInIndex;
use App\Livewire\Backend\Employee\Documents\AssignedDocuments;
use App\Livewire\Backend\Employee\Documents\ManageDocuments;
use App\Livewire\Backend\Employee\Leaves\LeavesIndexEmp;
use App\Livewire\Backend\Employee\Onboarding\OnboardingIndex;
use App\Livewire\Backend\Employee\Reports\ExpensesIndex;
use App\Livewire\Backend\Employee\Reports\PayslipIndex;
use App\Livewire\Backend\Employee\Schedule\ScheduleIndex;
use App\Livewire\Backend\Employee\Settings\ProfileSettings;
use App\Livewire\Backend\Employee\Settings\VerificationCentreSettings;
use App\Livewire\Backend\Employee\Training\TrainingIndexEmp;
use App\Livewire\Backend\Settings\PasswordSettings;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public routes for employee (no auth required)
|--------------------------------------------------------------------------
*/

Route::domain('{company}.' . config('app.base_domain'))
  ->middleware(['guest', 'checkCompanySubdomain'])
  ->name('employee.auth.')
  ->group(function () {
    Route::get('/employee-login', EmployeeLogin::class)->name('empLogin');
    Route::controller(SetPasswordController::class)->group(function () {
      Route::get('employee/set-password/{token}', 'showForm')->name('set-password');
      Route::post('employee/save-password/{token}',  'setPassword')->name('save-password');
    });
  });

/*
|--------------------------------------------------------------------------
| Authenticated employee dashboard routes
|--------------------------------------------------------------------------
*/
Route::domain('{company}.' . config('app.base_domain'))
  ->prefix('employee/dashboard')
  ->middleware(['auth', 'checkEmployee'])
  ->name('employee.dashboard.')
  ->group(function () {
    // Dashboard home
    Route::get('/', Dashboard::class)->name('index');


    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
      Route::get('password', PasswordSettings::class)->name('password');
      Route::get('verification-center', VerificationCentreSettings::class)->name('verification-center');
    });

    // profile manage
    Route::prefix('profile')->name('profile.')->group(function () {
      Route::get('/', ProfileSettings::class)->name('index');
    });


    // chat manage
    Route::prefix('chat')->name('chat.')->group(function () {
      Route::get('/', ChatIndex::class)->name('index');
    });


    Route::prefix('training')->name('training.')->group(function () {
      Route::get('/', TrainingIndexEmp::class)->name('index');
    });


    // CLOCK IN
    Route::prefix('clock-in')->name('clockin.')->group(function () {
      Route::get('/', ClockInIndex::class)->name('index');
    });

    // SCHEDULE
    Route::prefix('schedule')->name('schedule.')->group(function () {
      Route::get('/', ScheduleIndex::class)->name('index');
    });

    // LEAVES
    Route::prefix('leaves')->name('leaves.')->group(function () {
      Route::get('/', LeavesIndexEmp::class)->name('index');
    });


    Route::prefix('reports')->name('reports.')->group(function () {
      Route::get('/expenses', ExpensesIndex::class)->name('expenses');
      Route::get('/pay-slips', PayslipIndex::class)->name('payslips');
    });


    // ONBOARDING
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
      Route::get('/', OnboardingIndex::class)->name('index');
    });


    // documents manage
    Route::prefix('documents')->name('documents.')->group(function () {
      Route::get('/assigned', AssignedDocuments::class)->name('assigned');
      Route::get('/manage', ManageDocuments::class)->name('manage');
    });
  });

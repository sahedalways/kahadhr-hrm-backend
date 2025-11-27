<?php

use App\Http\Controllers\SetPasswordController;
use App\Livewire\Backend\Employee\Dashboard;
use App\Livewire\Backend\Employee\Auth\EmployeeLogin;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public routes for company (no auth required)
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
| Authenticated company dashboard routes
|--------------------------------------------------------------------------
*/
Route::domain('{company}.' . config('app.base_domain'))
  ->prefix('employee/dashboard')
  ->middleware(['auth', 'employee'])
  ->name('employee.dashboard.')
  ->group(function () {
    // Dashboard home
    Route::get('/', Dashboard::class)->name('index');
  });

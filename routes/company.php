<?php


use App\Livewire\Backend\Company\Auth\CompanyLogin;
use App\Livewire\Backend\Company\Dashboard;
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
    Route::get('/', Dashboard::class)->name('home');

    // Example: other dashboard pages
    // Route::get('profile', Profile::class)->name('profile');
    // Route::get('employees', Employees::class)->name('employees');
  });

<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Livewire\Backend\Admin\AdminEmpDetails;
use App\Livewire\Backend\Admin\Auth\AdminLogin;
use App\Livewire\Backend\Admin\BillingPayments;
use App\Livewire\Backend\Admin\CompanyDetails;
use App\Livewire\Backend\Admin\Dashboard;
use App\Livewire\Backend\Admin\ManageCompanies;
use App\Livewire\Backend\Admin\ManageEmployees;
use App\Livewire\Backend\Admin\Reports;
use App\Livewire\Backend\Admin\Repots\ReportingDutyIndex;
use App\Livewire\Backend\Admin\SupportTickets;
use App\Livewire\Backend\ContactInfo;
use App\Livewire\Backend\Settings\ChargeSettings;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\SecuritySettings;
use App\Livewire\Backend\Settings\SiteSettings;
use App\Livewire\Backend\Settings\SmsSettings;
use App\Livewire\Backend\Settings\SocialSettings;
use App\Livewire\Backend\Settings\TrialSettings;
use Illuminate\Support\Facades\Route;

// login route
Route::domain(config('app.admin_subdomain') . '.' . config('app.base_domain'))
  ->group(function () {
    Route::get('/', [AdminLogin::class, '__invoke'])->name('login');
  });

// Super Admin routes under admin.demo.com
Route::domain(config('app.admin_subdomain') . '.' . config('app.base_domain'))->prefix('dashboard')->middleware(['auth', 'superAdmin'])->name('super-admin.')->group(function () {

  // Dashboard
  Route::get('/', Dashboard::class)->name('home');

  Route::prefix('employees')->name('dashboard.')->group(function () {
    Route::get(
      '/details/{employee}',
      AdminEmpDetails::class
    )->name('employees.details');
  });

  // Manage Companies
  Route::prefix('companies')->group(function () {
    Route::get('/', ManageCompanies::class)->name('companies');
    Route::get('/employees', ManageEmployees::class)->name('employees');
    Route::get('/employees', ManageEmployees::class)->name('employees');


    Route::name('company.')->group(function () {
      Route::get('/details/{id}', CompanyDetails::class)->name('details.show');
    });
  });

  // Billing & Payments
  Route::get('/billing-payments', BillingPayments::class)
    ->name('billing');

  // Reports
  Route::get('/reports', Reports::class)
    ->name('reports');

  // Support Tickets
  Route::get('/support-tickets', SupportTickets::class)
    ->name('support');


  // Settings routes
  Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('site', SiteSettings::class)->name('site');
    Route::get('mail', MailSettings::class)->name('mail');
    Route::get('sms', SmsSettings::class)->name('sms');
    Route::get('security', SecuritySettings::class)->name('security');
    Route::get('social', SocialSettings::class)->name('social');
    Route::get('charge', ChargeSettings::class)->name('charge');
    Route::get('trial', TrialSettings::class)->name('trial');
  });


  // reports routes
  Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('reporting-duties', ReportingDutyIndex::class)->name('reporting-duties');
  });


  // for contact info
  Route::prefix('/contact-info')->name('contact-info.')->group(function () {
    Route::get('/', ContactInfo::class)->name('index');
  });
});

// kk

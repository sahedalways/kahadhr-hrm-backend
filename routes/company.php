<?php


use App\Http\Controllers\Company\EmployeeController;
use App\Livewire\Backend\Company\Auth\CompanyLogin;
use App\Livewire\Backend\Company\Chat\ChatIndex;
use App\Livewire\Backend\Company\Dashboard;
use App\Livewire\Backend\Company\DocumentManage\DocumentManageIndex;
use App\Livewire\Backend\Company\DocumentType\DocumentTypesIndex;
use App\Livewire\Backend\Company\Employees\UsersIndex;
use App\Livewire\Backend\Company\Leaves\LeavesIndex;
use App\Livewire\Backend\Company\ManageDepartments\ManageDepartments;
use App\Livewire\Backend\Company\ManageTeams\ManageTeams;
use App\Livewire\Backend\Company\Onboarding\OnboardingIndex;
use App\Livewire\Backend\Company\Reports\ReportsIndex;
use App\Livewire\Backend\Company\Schedule\ScheduleIndex;
use App\Livewire\Backend\Company\Settings\BankInfoSettings;
use App\Livewire\Backend\Company\Settings\CalendarYearSettings;
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
      Route::get('calendar-year', CalendarYearSettings::class)->name('calendar-year');
    });


    Route::prefix('employees')->name('employees.')->group(function () {
      Route::get('/', UsersIndex::class)->name('index');

      Route::controller(EmployeeController::class)->group(function () {

        Route::get('/details/{id}', 'empDetails')->name('details');

        Route::post('/change-password/{id}', 'changePassword')->name('changePassword');
        Route::delete('/documents/delete/{id}', 'destroy');
      });
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


    Route::prefix('training')->name('training.')->group(function () {
      Route::get('/', TrainingIndex::class)->name('index');
    });

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
      Route::get('/', OnboardingIndex::class)->name('index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
      Route::get('/', ReportsIndex::class)->name('index');
    });


    Route::prefix('departments')->name('departments.')->group(function () {
      Route::get('/', ManageDepartments::class)->name('index');
    });


    Route::prefix('teams')->name('teams.')->group(function () {
      Route::get('/', ManageTeams::class)->name('index');
    });


    Route::prefix('document-types')->name('document-types.')->group(function () {
      Route::get('/', DocumentTypesIndex::class)->name('index');
    });


    Route::prefix('document-manage')->name('document-manage.')->group(function () {
      Route::get('/', DocumentManageIndex::class)->name('index');
    });
  });

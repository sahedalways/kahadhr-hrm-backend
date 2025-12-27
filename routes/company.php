<?php


use App\Http\Controllers\Company\EmployeeController;
use App\Http\Controllers\Company\OnboardingController;
use App\Livewire\Backend\Chat\ChatIndex;
use App\Livewire\Backend\Company\Auth\CompanyLogin;
use App\Livewire\Backend\Company\Dashboard;
use App\Livewire\Backend\Company\DocumentManage\DocumentManageIndex;
use App\Livewire\Backend\Company\DocumentType\DocumentTypesIndex;
use App\Livewire\Backend\Company\Employees\EmployeeDetails;
use App\Livewire\Backend\Company\Employees\UsersIndex;
use App\Livewire\Backend\Company\Leaves\LeaveSettings;
use App\Livewire\Backend\Company\Leaves\LeavesIndex;
use App\Livewire\Backend\Company\ManageTeamsDepartment\ManageTeamsDepartment;
use App\Livewire\Backend\Company\Onboarding\OnboardingIndex;
use App\Livewire\Backend\Company\Reports\CompanyExpenses;
use App\Livewire\Backend\Company\Reports\CompanyInvoice;
use App\Livewire\Backend\Company\Reports\CompanyPayslip;
use App\Livewire\Backend\Company\Schedule\ScheduleIndex;
use App\Livewire\Backend\Company\Settings\BankInfoSettings;
use App\Livewire\Backend\Company\Settings\CalendarYearSettings;
use App\Livewire\Backend\Company\Settings\ProfileSettings;
use App\Livewire\Backend\Company\Settings\VerificationCentreSettings;
use App\Livewire\Backend\Company\Timesheet\TimesheetIndex;
use App\Livewire\Backend\Company\Training\TrainingIndex;
use App\Livewire\Backend\Settings\MailSettings;
use App\Livewire\Backend\Settings\PasswordSettings;
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
  ->middleware(['auth', 'companyAdmin', 'checkSuspended'])
  ->name('company.dashboard.')
  ->group(function () {
    // Dashboard home
    Route::get('/', Dashboard::class)->name('index');

    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
      // Route::get('mail', MailSettings::class)->name('mail');
      // Route::get('sms', SmsSettings::class)->name('sms');
      Route::get('password', PasswordSettings::class)->name('password');
      Route::get('profile', ProfileSettings::class)->name('profile');
      Route::get('bank-info', BankInfoSettings::class)->name('bank-info');
      Route::get('mail', MailSettings::class)->name('mail');
      Route::get('verification-center', VerificationCentreSettings::class)->name('verification-center');
      Route::get('calendar-year', CalendarYearSettings::class)->name('calendar-year');
    });


    Route::prefix('employees')->name('employees.')->group(function () {
      Route::get('/', UsersIndex::class)->name('index');

      Route::controller(EmployeeController::class)->group(function () {
        Route::post('/change-password/{id}', 'changePassword')->name('changePassword');
        Route::delete('/documents/delete/{id}', 'destroy');
      });


      Route::get(
        '/details/{employee}',
        EmployeeDetails::class
      )->name('details');
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
      Route::get('/manage', LeavesIndex::class)->name('index');


      Route::get('settings', LeaveSettings::class)
        ->name('settings');
    });


    Route::prefix('training')->name('training.')->group(function () {
      Route::get('/', TrainingIndex::class)->name('index');
    });

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
      Route::get('/', OnboardingIndex::class)->name('index');
    });


    Route::prefix('reports')->name('reports.')->group(function () {
      Route::get('/expenses', CompanyExpenses::class)->name('expenses');
      Route::get('/pay-slips', CompanyPayslip::class)->name('payslips');
      Route::get('/invoices', CompanyInvoice::class)->name('invoices');
    });


    Route::prefix('teams-departments')->name('teams-departments.')->group(function () {
      Route::get('/', ManageTeamsDepartment::class)->name('index');
    });


    Route::prefix('document-types')->name('document-types.')->group(function () {
      Route::get('/', DocumentTypesIndex::class)->name('index');
    });


    Route::prefix('document-manage')->name('document-manage.')->group(function () {
      Route::get('/', DocumentManageIndex::class)->name('index');
    });


    Route::get('/onboarding/view/{id}', [OnboardingController::class, 'view'])
      ->name('onboarding.view');
  });


Route::get('/employees/csv-template', function () {
  $headers = [
    'Content-Type' => 'text/csv',
    'Content-Disposition' => 'attachment; filename="employee_import_template.csv"',
  ];

  $callback = function () {
    $file = fopen('php://output', 'w');

    fputcsv($file, ['f_name', 'l_name', 'email', 'department', 'role']);


    fputcsv($file, ['John', 'Doe', 'john@example.com', 'HR', 'Employee']);

    fclose($file);
  };

  return response()->stream($callback, 200, $headers);
})->name('employees.csv.template');

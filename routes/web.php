<?php


// all those super admin routes below

use App\Jobs\SendTestMailJob;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';


// all those company routes below
require __DIR__ . '/company.php';


// all those employee routes below
require __DIR__ . '/employee.php';


// dev tools routes below
require __DIR__ . '/dev-tools.php';




Route::get('/trial-expired', function () {

  if (auth()->check()) {
    $company = auth()->user()->company;


    if (
      $company->subscription_status === 'trial' &&
      $company->trial_ends_at &&
      Carbon::parse($company->trial_ends_at)->isFuture()
    ) {
      return redirect()->route(
        'company.dashboard.index',
        ['company' => $company->sub_domain]
      );
    }
  }

  return view('subscription.trial-expired');
})->name('subscription.expired');


Route::get('/password-set-success', function () {
  return view('auth.password-set-success', [
    'title' => 'Password Changed!',
    'message' => 'You can now login using your new password.',
    'user_type' => request()->get('user_type', 'Company'),
  ]);
})->name('password.set.success');

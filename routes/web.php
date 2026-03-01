<?php


// all those super admin routes below

use App\Jobs\SendTestMailJob;
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
  if (auth()->check() && auth()->user()->company->subscription_status === 'active') {
    return redirect()->route(
      'company.dashboard.index',
      ['company' => auth()->user()->company->sub_domain]
    );
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


Route::get('/send-test-mail', function () {
  SendTestMailJob::dispatch()->delay(now()->addSeconds(10));

  return 'Queued mail dispatched! Check after 10 seconds.';
});

// Route::get('/send-test-sms', function () {
//   try {
//     $twilio = new Client(
//       env('TWILIO_SID'),
//       env('TWILIO_AUTH_TOKEN')
//     );

//     $message = $twilio->messages->create(
//       '+8801616516753',
//       [
//         'from' => env('TWILIO_PHONE_NUMBER'),
//         'body' => 'This is a test SMS from KahadHR application!'
//       ]
//     );

//     return "Test SMS sent successfully to +8801616516753! SID: " . $message->sid;
//   } catch (\Exception $e) {
//     return "Error: " . $e->getMessage();
//   }
// });

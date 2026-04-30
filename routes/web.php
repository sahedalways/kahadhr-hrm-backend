<?php


use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';


// all those company routes below
require __DIR__ . '/company.php';


// all those employee routes below
require __DIR__ . '/employee.php';


// dev tools routes below
require __DIR__ . '/dev-tools.php';


// subscription routes below
require __DIR__ . '/subscription.php';





Route::get('/password-set-success', function () {
  return view('auth.password-set-success', [
    'title' => 'Password Changed!',
    'message' => 'You can now login using your new password.',
    'user_type' => request()->get('user_type', 'Company'),
  ]);
})->name('password.set.success');

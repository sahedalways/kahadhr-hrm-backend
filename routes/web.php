<?php


// all those super admin routes below

use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';


// all those company routes below
require __DIR__ . '/company.php';


// all those employee routes below
require __DIR__ . '/employee.php';


// dev tools routes below
require __DIR__ . '/dev-tools.php';


Route::get('/trial-expired', function () {
  return view('subscription.trial-expired');
})->name('subscription.expired');

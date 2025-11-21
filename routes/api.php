<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\ContactController;
use Illuminate\Support\Facades\Route;


Route::middleware(['cors'])->group(function () {
  Route::controller(AuthController::class)->group(function () {

    // for authentication routes
    Route::post('register', 'register')->middleware();
  });



  // for contact us route
  Route::post('/save-contact', [ContactController::class, 'store'])->middleware('throttle:2,1');
});



// For inquiries regarding mobile or web application development, feel free to reach out!

// 📧 Email: ssahed65@gmail.com
// 📱 WhatsApp: +8801616516753
// My name is Sk Sahed Ahmed, and I look forward to collaborating with you!
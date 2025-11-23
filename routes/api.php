<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\HomeController;
use Illuminate\Support\Facades\Route;


Route::middleware(['cors'])->group(function () {


  Route::prefix('auth')->controller(AuthController::class)->group(function () {

    // Registration route
    Route::post('register', 'register');

    // Send OTP route
    Route::post('send-email-otp', 'sendEmailOtp');
    Route::post('send-phone-otp', 'sendPhoneOtp');

    // resend email OTP route
    Route::post('resend-email-otp', 'resendEmailOtp');

    // verify otp
    Route::post('verify-otp', 'verifyOtp');
  });



  // get home data api
  Route::controller(HomeController::class)->group(function () {
    Route::get('home-data', 'getHomeData');
  });


  // for contact us route
  Route::post('/contact/submit', [ContactController::class, 'store'])->middleware('throttle:2,1');
});



// For inquiries regarding mobile or web application development, feel free to reach out!

// 📧 Email: ssahed65@gmail.com
// 📱 WhatsApp: +8801616516753
// My name is Sk Sahed Ahmed, and I look forward to collaborating with you!
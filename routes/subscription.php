

<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;



Route::get('/trial-expired', [SubscriptionController::class, 'trialExpired'])
    ->name('subscription.expired');


Route::get('/subscription-suspended', [SubscriptionController::class, 'suspended'])
    ->name('subscription.suspended');

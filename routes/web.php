<?php

use App\Livewire\Backend\Auth\Login;
use Illuminate\Support\Facades\Route;

// login route
Route::get('/', [Login::class, '__invoke'])->name('login');


// all those super admin routes below
require __DIR__ . '/super-admin.php';


// dev tools routes below
require __DIR__ . '/dev-tools.php';
<?php

use App\Livewire\Backend\Company\Dashboard;
use Illuminate\Support\Facades\Route;



Route::domain('{company}.' . config('app.base_domain'))->middleware(['auth', 'companyAdmin'])->name('company.')->group(function () {
  Route::get('dashboard', Dashboard::class)->name('dashboard');
});

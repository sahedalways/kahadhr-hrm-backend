<?php

namespace App\Providers;

use App\Models\EmployeeProfile;
use App\Observers\EmployeeProfileObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        EmployeeProfile::observe(EmployeeProfileObserver::class);
    }
}

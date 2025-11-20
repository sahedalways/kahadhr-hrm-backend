<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * 🌐 Global limit — public
         */
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        /**
         * 🔐 Login route limiter
         *  
         */
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

        /**
         * 🧾 Register route limiter
         * 
         */
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        /**
         * 🔁 OTP / Forgot password limiter — 
         * => 
         */
        RateLimiter::for('otp', function (Request $request) {
            $identifier = $request->input('email') ?? $request->ip();
            return Limit::perMinute(3)->by($identifier);
        });
    }
}

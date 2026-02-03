<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\ShareAuthUser::class);

        $middleware->alias([
            'superAdmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'companyAdmin' => \App\Http\Middleware\CompanyAdmin::class,
            'checkEmployee' => \App\Http\Middleware\CheckEmployee::class,
            'checkSuspended' => \App\Http\Middleware\CheckCompanySuspended::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'guest' => \App\Http\Middleware\RedirectIfCompanyorEmployeeAuthenticated::class,
            'log.traffic' => \App\Http\Middleware\LogTraffic::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */ protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        $domain = $request->getHost();
        $adminSub = config('app.admin_subdomain');

        $parts = explode('.', $domain);
        $sub = $parts[0] ?? null;

        $user = auth()->user();

        // Admin domain
        if ($sub === $adminSub) {
            return route('login');
        }

        if ($sub === 'company') {
            $path = $request->path();

            if (str_starts_with($path, 'employee')) {

                if ($user && $user->user_type === 'employee') {
                    return route('employee.dashboard.index');
                }


                return route('employee.auth.empLogin', [
                    'company' => 'company'
                ]);
            }

            if ($user && $user->user_type === 'company') {
                return route('company.dashboard.index');
            }

            return route('company.auth.login');
        }

        return '/';
    }
}

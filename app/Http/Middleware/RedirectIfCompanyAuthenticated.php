<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfCompanyAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = 'web')
    {
        // Check if company admin is logged in
        if (Auth::guard($guard)->check()) {
            return redirect()->route('company.dashboard.index', ['company' => app('authUser')->company->sub_domain]);
        }

        return $next($request);
    }
}

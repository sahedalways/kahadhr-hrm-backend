<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfCompanyorEmployeeAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = 'web')
    {
        $authUser = app('authUser');

        if ($authUser) {
            if ($authUser->user_type === 'company' && $authUser->company?->sub_domain) {
                return redirect()->route(
                    'company.dashboard.index',
                    ['company' => $authUser->company->sub_domain]
                );
            } elseif ($authUser->user_type === 'employee' && $authUser->employee?->company?->sub_domain) {
                return redirect()->route(
                    'employee.dashboard.index',
                    ['company' => $authUser->employee->company->sub_domain]
                );
            }
        }

        return $next($request);
    }
}

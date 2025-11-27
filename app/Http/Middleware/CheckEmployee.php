<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CheckEmployee
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('company');
        $user = Auth::user();

        if (!$user || $user->employee->company->sub_domain !== $subdomain) {
            abort(403, 'Unauthorized access for this subdomain.');
        }

        if ($user->user_type !== 'employee') {
            abort(403, 'This page is only accessible by employees.');
        }

        return $next($request);
    }
}

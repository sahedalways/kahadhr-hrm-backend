<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompanyAdmin
{
    public function handle(Request $request, Closure $next)
    {

        $subdomain = $request->route('company');
        $user = Auth::user();


        if ($user->user_type == 'company') {
            if (!$user || $user->company->sub_domain !== $subdomain) {
                abort(403, 'Unauthorized access for this subdomain.');
            }
        } else {
            abort(403, 'This page is only accessible by company.');
        }




        return $next($request);
    }
}

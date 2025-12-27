<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompanyAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();


        if ($user->user_type !== 'company') {
            abort(403, 'This page is only accessible by company.');
        }




        return $next($request);
    }
}

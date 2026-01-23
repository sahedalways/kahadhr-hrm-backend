<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has an 'admin' role
        if (Auth::check() && Auth::user()->user_type === 'superAdmin') {
            return $next($request);
        }

        // Redirect to home or an error page if the user is not an admin
        return redirect('/')->with('error', 'You do not have super admin access.');
    }
}
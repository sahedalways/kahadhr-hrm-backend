<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanySuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $company = auth()->user()->company ?? null;

        // যদি company suspended থাকে
        if ($company && $company->subscription_status === 'suspended') {
            abort(403, 'Your account is suspended. Please contact support.');
        }

        return $next($request);
    }
}

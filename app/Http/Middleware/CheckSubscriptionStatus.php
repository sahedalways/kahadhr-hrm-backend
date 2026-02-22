<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !$user->company) {
            return $next($request);
        }

        $company = $user->company;

        if (
            $company->subscription_status === 'trial' &&
            Carbon::today()->greaterThan(Carbon::parse($company->subscription_end))
        ) {
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}

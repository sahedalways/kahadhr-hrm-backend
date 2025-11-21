<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySubdomainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('company');

        // logged-in user
        $user = Auth::user();

        if (!$user) {
            // Not logged in
            return redirect()->route('company.login', ['company' => $subdomain]);
        }

        // check if user's company matches subdomain
        if ($user->company->subdomain !== $subdomain) {
            abort(403, 'Unauthorized access to this company.');
        }

        return $next($request);
    }
}

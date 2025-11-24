<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;

class CheckCompanySubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('company');


        $company = Company::where('sub_domain', $subdomain)->first();

        if (!$company) {
            abort(404, 'Company not found for this subdomain.');
        }

        // চাইলে $request এ company inject করতে পারেন
        $request->merge(['company_model' => $company]);

        return $next($request);
    }
}

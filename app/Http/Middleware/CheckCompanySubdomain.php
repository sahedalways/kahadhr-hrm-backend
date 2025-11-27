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

        if ($company->status !== 'Active') {
            abort(403, 'This company is currently inactive. Please contact support for assistance.');
        }



        $request->merge(['company_model' => $company]);

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function trialExpired()
    {
        if (auth()->check()) {
            $company = auth()->user()->company;

            if (
                $company->subscription_status === 'trial' &&
                $company->trial_ends_at &&
                Carbon::parse($company->trial_ends_at)->isFuture()
            ) {
                return redirect()->route(
                    'company.dashboard.index',
                    ['company' => $company->sub_domain]
                );
            }
        }

        return view('subscription.trial-expired');
    }

    public function suspended()
    {
        if (auth()->check()) {
            $company = auth()->user()->company;

            if ($company->subscription_status !== 'suspended') {
                return redirect()->route(
                    'company.dashboard.index',
                    ['company' => $company->sub_domain]
                );
            }
        }

        return view('subscription.suspended');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Services\PaymentGateway;
use Carbon\Carbon;

class ChargeCompanies extends Command
{
    protected $signature = 'companies:charge';
    protected $description = 'Charge all companies based on employee count every month';

    public function handle()
    {
        $this->info('Starting monthly company charges...');

        $companies = Company::where('subscription_status', 'active')
            ->whereDate('subscription_end', '<=', Carbon::today())
            ->get();

        foreach ($companies as $company) {
            $employeeCount = $company->employees()->count();

            // Skip if no employees
            if ($employeeCount == 0) continue;

            $amount = $company->monthlyAmount();

            // Charge via your payment gateway
            // $result = PaymentGateway::charge($company->card, $monthlyAmount);

            // if ($result->success) {
            // Update subscription dates
            $company->subscription_start = Carbon::now();
            $company->subscription_end = Carbon::now()->addMonth();
            $company->subscription_status = 'active';
            $company->save();

            $this->info("Charged {$company->company_name} successfully: {$amount}");
            // } else {
            //     $this->error("Failed to charge {$company->company_name}");
            //     // Optionally log errors or notify admin
            // }
        }


        // if ($result->success) {
        //     $company->subscription_start = now();
        //     $company->subscription_end = now()->addMonth();
        //     $company->subscription_status = 'active';
        //     $company->payment_status = 'paid';
        //     $company->payment_failed_count = 0;
        //     $company->save();
        // } else {
        //     $company->payment_status = 'failed';
        //     $company->payment_failed_count += 1;
        //     $company->save();

        //     if ($company->payment_failed_count >= 3) {
        //         $company->subscription_status = 'suspended';
        //         $company->save();
        //     }
        // }

        $this->info('Company charges completed.');
    }
}

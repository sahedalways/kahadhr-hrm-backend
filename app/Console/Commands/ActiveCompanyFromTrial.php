<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActiveCompanyFromTrial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:active-company-from-trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate companies whose trial period has ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Checking trial companies...');

            $companies = Company::where('subscription_status', 'trial')
                ->whereNotNull('trial_ends_at')
                ->whereDate('trial_ends_at', '<=', Carbon::today())
                ->get();

            if ($companies->isEmpty()) {
                $this->info('No trial companies to activate.');
                return Command::SUCCESS;
            }

            foreach ($companies as $company) {
                $company->subscription_status = 'active';
                $company->subscription_start = Carbon::today();
                $company->subscription_end = Carbon::today()->addMonth();
                $company->payment_status = 'pending';
                $company->save();

                $this->info("Activated: {$company->company_name}");
            }

            $this->info('Trial activation completed.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            \Log::error('Trial activation error: ' . $e->getMessage());
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}

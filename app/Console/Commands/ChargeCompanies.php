<?php

namespace App\Console\Commands;

use App\Jobs\PaymentStatusEmailJob;
use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\CompanyChargeRate;
use App\Models\Employee;
use App\Models\Invoice;
use App\Services\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChargeCompanies extends Command
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        parent::__construct();
        $this->gateway = $gateway;
    }


    protected $signature = 'companies:charge';
    protected $description = 'Charge all companies based on employee count every month';

    public function handle()
    {
        $this->info('Starting monthly company charges...');

        $companies = Company::where('subscription_status', 'active')
            ->whereDate('subscription_end', '<=', Carbon::today())
            ->get();


        $rate = optional(CompanyChargeRate::first())->rate;

        if (!$rate) {
            $this->error('No company charge rate found. Skipping charges.');
            return;
        }


        foreach ($companies as $company) {

            $minCharge = config('billing.minimum_monthly_charge', 50);
            $employeeCount = Employee::withoutGlobalScope('filterByUserType')
                ->where('company_id', $company->id)
                ->whereDate('billable_from', '<=', now()->endOfMonth())
                ->count();

            if ($employeeCount == 0) {
                $amount = $minCharge;
                $this->info("No employees found for {$company->company_name}. Applying minimum charge: {$minCharge} GBP");
            } else {
                $amount = $employeeCount * $rate;
            }

            $vat = 0;
            $total = $amount + $vat;

            $card = $company->defaultCard();



            if (!$card || !$card->stripe_payment_method_id) {
                $this->error("No valid card for {$company->company_name}");
                continue;
            }

            $result = $this->gateway->charge(
                $card->stripe_payment_method_id,
                $amount
            );


            if ($result->success) {
                $company->subscription_start = now();
                $company->subscription_end = now()->addMonth();
                $company->subscription_status = 'active';
                $company->payment_status = 'paid';
                $company->payment_failed_count = 0;
                $company->save();


                $invoiceNumber = 'INV-' . strtoupper(uniqid());

                Invoice::create([
                    'company_id' => $company->id,
                    'billing_period_start' => now()->startOfMonth(),
                    'billing_period_end' => now()->endOfMonth(),
                    'employee_fee' => $rate,
                    'total_employees_billed' => $employeeCount,
                    'subtotal' => $amount,
                    'total' => $total,
                    'vat' => $vat,
                    'invoice_date' => now(),
                    'invoice_number' => $invoiceNumber,
                    'currency' => 'GBP',
                    'status' => 'paid',
                ]);

                $this->info("Charged and invoiced {$company->company_name}: {$total} GBP");
            } else {
                $company->payment_status = 'failed';
                $company->payment_failed_count += 1;
                $company->save();

                $company->notify('payment_failed', [
                    'message' => 'Payment attempt failed. Please update your card.',
                    'failed_attempts' => $company->payment_failed_count,
                ]);

                PaymentStatusEmailJob::dispatch($company->id, 'payment_failed');


                if ($company->payment_failed_count >= 3) {
                    $company->subscription_status = 'suspended';
                    $company->save();

                    $company->notify('subscription_suspended', [
                        'message' => 'Your subscription has been suspended due to multiple failed payments.',
                    ]);

                    PaymentStatusEmailJob::dispatch($company->id, 'subscription_suspended');
                }
            }

            $this->info('Company charges completed.');
        }
    }
}

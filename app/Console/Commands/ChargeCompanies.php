<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\CompanyChargeRate;
use App\Models\Invoice;
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
            $employeeCount = $company->activeEmployees()->count();


            // Skip if no employees
            if ($employeeCount == 0) continue;

            $amount = $company->monthlyAmount();

            $card = $company->defaultCard();

            if (!$card || !$card->stripe_payment_method_id) {
                $this->error("No valid card for {$company->company_name}");
                continue;
            }

            $result = PaymentGateway::charge(
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
                $subtotal = $company->monthlyAmount();
                $vat = 0;
                $total = $subtotal + $vat;
                $rate = CompanyChargeRate::first()->value('rate');

                Invoice::create([
                    'company_id' => $company->id,
                    'billing_period_start' => now()->startOfMonth(),
                    'billing_period_end' => now()->endOfMonth(),
                    'employee_fee' => $rate,
                    'total_employees_billed' => $employeeCount,
                    'subtotal' => $subtotal,
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

                if ($company->payment_failed_count >= 3) {
                    $company->subscription_status = 'suspended';
                    $company->save();
                }
            }

            $this->info('Company charges completed.');
        }
    }
}

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
            $employeeCount = Employee::where('company_id', $company->id)
                ->whereDate('billable_from', '<=', now()->endOfMonth())
                ->count();

            // Skip if no employees
            if ($employeeCount == 0) continue;

            $rate = CompanyChargeRate::first()->rate;
            $amount = $employeeCount * $rate;

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

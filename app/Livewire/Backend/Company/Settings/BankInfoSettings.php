<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Jobs\InvoiceEmailJob;
use App\Jobs\PaymentStatusEmailJob;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Models\CompanyBankInfo;
use App\Models\CompanyChargeRate;
use App\Models\Employee;
use Carbon\Carbon;
use Stripe\StripeClient;
use App\Models\Invoice;
use App\Services\PaymentGateway;
use App\Traits\Exportable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class BankInfoSettings extends BaseComponent
{
    use WithPagination, Exportable;
    public $bank_name;
    public $stripe_payment_method_id;
    public $card_brand;
    public $card_last4;
    public $card_exp_month;
    public $card_exp_year;
    public $card_holder_name;

    public $companyBankInfo;
    public $company;



    public $company_id;
    public $search;

    // Filters
    public $filterStatus = '';
    public $filterDate = '';
    public $date_from;
    public $date_to;

    public $sortOrder = 'desc';
    public $perPage = 20;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $subscription_status;
    public $payment_status;
    public $subscription_start;
    public $subscription_end;
    public $trial_ends_at;

    protected $listeners = [
        'stripePaymentMethodCreated' => 'save'
    ];

    public function mount()
    {
        $this->company = app('authUser')->company;
        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        $this->companyBankInfo = CompanyBankInfo::where('company_id', $this->company->id)->first();

        if ($this->companyBankInfo) {
            $this->bank_name = $this->companyBankInfo->bank_name;
            $this->stripe_payment_method_id = $this->companyBankInfo->stripe_payment_method_id;


            if ($this->stripe_payment_method_id) {
                $this->fetchStripeCardInfo();
            }
        }

        $this->company_id = auth()->user()->company->id;



        $this->subscription_status = $this->company->subscription_status ?? 'expired';
        $this->payment_status = $this->company->payment_status ?? 'pending';
        $this->subscription_start = $this->company->subscription_start;
        $this->subscription_end = $this->company->subscription_end;
        $this->trial_ends_at = $this->company->trial_ends_at ?? null;



        $this->loaded = collect();
        $this->loadMore();
    }


    public function save($paymentMethodId)
    {
        $validatedData = [
            'company_id' => $this->company->id,
            'stripe_payment_method_id' => $paymentMethodId,
        ];

        if ($this->companyBankInfo) {
            $this->companyBankInfo->update($validatedData);
        } else {
            $this->companyBankInfo = CompanyBankInfo::create($validatedData);
        }

        $this->stripe_payment_method_id = $paymentMethodId;

        $this->fetchStripeCardInfo();

        $isTrialCompany = Company::where('id', $this->company->id)
            ->where('subscription_status', 'trial')
            ->first();

        if ($isTrialCompany) {
            $isTrialCompany->update([
                'subscription_status' => 'active',
                'subscription_start' => Carbon::today(),
                'subscription_end' => Carbon::today()->addMonth(),
                'payment_status' => 'unpaid',
            ]);
        }

        $company = $this->company->fresh();


        Cache::lock("charge-once-{$company->id}", 300)->get(function () use ($company) {

            if (
                $company->subscription_status === 'active' &&
                $company->subscription_end &&
                \Carbon\Carbon::parse($company->subscription_end)->lte(\Carbon\Carbon::today())
            ) {
                $this->chargeCompanyImmediately($company);
            }
        });

        $this->dispatch('closemodal');

        $this->toast('Card Payment Info Saved Successfully!', 'success');
    }




    public function fetchStripeCardInfo()
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentMethod = $stripe->paymentMethods->retrieve($this->stripe_payment_method_id);

        $card = $paymentMethod->card;

        $this->card_brand = $card->brand;
        $this->card_last4 = $card->last4;
        $this->card_exp_month = $card->exp_month;
        $this->card_exp_year = $card->exp_year;
        $this->card_holder_name = $paymentMethod->billing_details->name;
    }



    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Invoice::where('company_id', $this->company_id);

        if ($this->search) {
            $query->where('invoice_number', 'like', "%{$this->search}%");
        }

        // Date Filter
        switch ($this->filterDate) {
            case 'day':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($this->date_from && $this->date_to) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($this->date_from)->startOfDay(),
                        Carbon::parse($this->date_to)->endOfDay()
                    ]);
                }
                break;
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleStatusFilter($value)
    {
        $this->filterStatus = $value;
        $this->resetLoaded();
    }

    public function handleDateFilter($value)
    {
        $this->filterDate = $value;
        $this->resetLoaded();
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function handleDateFrom($value)
    {
        $this->date_from = $value;
        if ($this->filterDate === 'custom') {
            $this->resetLoaded();
        }
    }

    public function handleDateTo($value)
    {
        $this->date_to = $value;
        if ($this->filterDate === 'custom') {
            $this->resetLoaded();
        }
    }


    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            $this->toast('Invoice not found!', 'error');
            return;
        }

        $pdf = Pdf::loadView('exports.single-invoice-pdf', [
            'invoice' => $invoice
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $invoice->invoice_number . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    // ─── EXPORT ───────────────────────────────────────────────────────
    public function exportInvoices($type)
    {
        $data = Invoice::where('company_id', $this->company_id)
            ->get()
            ->map(function ($d) {
                return [
                    'Invoice #' => $d->invoice_number,
                    'Billing Period' => $d->billing_period_start->format('d M, Y') . ' - ' . $d->billing_period_end->format('d M, Y'),
                    'Employee Fee' => number_format($d->employee_fee, 2),
                    'Total Employees' => $d->total_employees_billed,
                    'Subtotal' => number_format($d->subtotal, 2),
                    'VAT' => number_format($d->vat, 2),
                    'Total' => number_format($d->total, 2),
                    'Status' => ucfirst($d->status),
                    'Invoice Date' => $d->created_at->format('d M, Y'),
                ];
            });

        return $this->export(
            $data,
            $type,
            'company-invoices',
            'exports.generic-table-pdf',
            [
                'title' => 'Company Invoices Report',
                'columns' => ['Invoice #', 'Billing Period', 'Employee Fee', 'Total Employees', 'Subtotal', 'VAT', 'Total', 'Status', 'Invoice Date'],
                'keys' => ['Invoice #', 'Billing Period', 'Employee Fee', 'Total Employees', 'Subtotal', 'VAT', 'Total', 'Status', 'Invoice Date'],
            ]
        );
    }


    public function chargeCompanyImmediately($company)
    {
        $rate = optional(CompanyChargeRate::first())->rate;

        if (!$rate) {
            return;
        }


        $minCharge = config('billing.minimum_monthly_charge', 50);

        $employeeCount = Employee::withTrashed()
            ->withoutGlobalScope('filterByUserType')
            ->where('company_id', $company->id)
            ->where('is_billable', true)
            ->whereDate('billable_from', '<=', now()->endOfMonth())
            ->count();

        if ($employeeCount == 0) {
            $amount = $minCharge;
        } else {
            $amount = $employeeCount * $rate;
        }

        $vat = 0;
        $total = $amount + $vat;

        $card = $company->defaultCard();



        if (!$card || !$card->stripe_payment_method_id) {
            $company->payment_status = 'failed';
            $company->increment('payment_failed_count');
            $company->refresh();


            $company->update([
                'payment_status' => 'failed',
            ]);


            $company->notify('payment_failed', [
                'message' => 'Payment attempt failed. Please update your card.',
                'failed_attempts' => $company->payment_failed_count,
            ]);

            PaymentStatusEmailJob::dispatch($company->id, 'payment_failed');

            if ($company->payment_failed_count >= 3) {
                $company->update([
                    'subscription_status' => 'suspended',
                ]);


                $company->notify('subscription_suspended', [
                    'message' => 'Your subscription has been suspended due to multiple failed payments.',
                ]);

                PaymentStatusEmailJob::dispatch($company->id, 'subscription_suspended');
            }


            return;
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

            $invoice = Invoice::create([
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

            Employee::onlyTrashed()
                ->withoutGlobalScope('filterByUserType')
                ->where('company_id', $company->id)
                ->forceDelete();


            Employee::query()
                ->withoutGlobalScope('filterByUserType')
                ->where('company_id', $company->id)
                ->whereNull('deleted_at')
                ->update([
                    'billable_from' => now()->addDays(3),
                    'is_billable' => false,
                    'updated_at' => now(),
                ]);

            InvoiceEmailJob::dispatch($invoice->id, $company->id);

            Log::info(
                "Charged and invoiced {$company->company_name}: {$total} GBP",
                [
                    'company_id' => $company->id,
                    'amount' => $amount,
                    'total' => $total,
                    'date' => now()->toDateTimeString(),
                ]
            );
        } else {
            $company->payment_status = 'failed';
            $company->increment('payment_failed_count');
            $company->refresh();


            $company->update([
                'payment_status' => 'failed',
            ]);


            $company->notify('payment_failed', [
                'message' => 'Payment attempt failed. Please update your card.',
                'failed_attempts' => $company->payment_failed_count,
            ]);

            PaymentStatusEmailJob::dispatch($company->id, 'payment_failed');

            if ($company->payment_failed_count >= 3) {
                $company->update([
                    'subscription_status' => 'suspended',
                ]);


                $company->notify('subscription_suspended', [
                    'message' => 'Your subscription has been suspended due to multiple failed payments.',
                ]);

                PaymentStatusEmailJob::dispatch($company->id, 'subscription_suspended');
            }
        }

        return false;
    }



    public function render()
    {
        return view('livewire.backend.company.settings.bank-info-settings', [
            'invoices' => $this->loaded,
        ]);
    }
}

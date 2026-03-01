<?php
// app/Jobs/InvoiceEmailJob.php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $invoiceId;
    protected int $companyId;

    public function __construct(int $invoiceId, int $companyId)
    {
        $this->invoiceId = $invoiceId;
        $this->companyId = $companyId;
    }

    public function handle(): void
    {
        $invoice = Invoice::with('company')->find($this->invoiceId);
        $company = $invoice?->company;

        if (!$invoice || !$company) {
            \Log::error("Invoice #{$this->invoiceId} or Company #{$this->companyId} not found");
            return;
        }

        $subject = "Invoice {$invoice->invoice_number} – Payment Successful";

        try {
            Mail::send('mail.invoice_paid', [
                'invoice' => $invoice,
                'company' => $company,
            ], function ($message) use ($company, $subject) {
                $message->to($company->company_email)
                    ->subject($subject);
            });

            \Log::info("Invoice email sent to {$company->company_email} for invoice {$invoice->invoice_number}");
        } catch (\Exception $e) {
            \Log::error("Failed to send invoice email: " . $e->getMessage());
        }
    }
}

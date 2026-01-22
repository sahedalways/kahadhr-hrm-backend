<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PaymentStatusEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $companyId;
    protected string $type;

    /**
     * $type = payment_failed | subscription_suspended
     */
    public function __construct(int $companyId, string $type)
    {
        $this->companyId = $companyId;
        $this->type = $type;
    }

    public function handle(): void
    {
        $company = Company::find($this->companyId);

        if (! $company) {
            \Log::error("Company #{$this->companyId} not found for payment email.");
            return;
        }

        $subject = match ($this->type) {
            'subscription_suspended' => 'Subscription Suspended – Action Required',
            'card_reminder' => 'Subscribe Now! – Subscription Ending Soon',
            default => 'Payment Failed – Please Update Card',
        };

        try {
            Mail::send('mail.payment_status', [
                'company' => $company,
                'type' => $this->type,
            ], function ($message) use ($company, $subject) {
                $message->to($company->company_email)
                    ->subject($subject);
            });

            \Log::info("Payment email ({$this->type}) sent to {$company->company_email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send payment email: " . $e->getMessage());
        }
    }
}

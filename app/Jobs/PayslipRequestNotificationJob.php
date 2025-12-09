<?php

namespace App\Jobs;

use App\Models\PaySlipRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PayslipRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $requestId;

    public function __construct(int $requestId)
    {
        $this->requestId = $requestId;
    }

    public function handle(): void
    {
        $req = PaySlipRequest::with('user', 'company')->find($this->requestId);

        if (!$req || !$req->company) return;

        try {
            Mail::send('mail.payslip_request', [
                'request' => $req,
                'company' => $req->company,
                'user'    => $req->user,
            ], function ($message) use ($req) {
                $message->to($req->company->company_email)
                    ->subject('New Payslip Request Received');
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send payslip request email: " . $e->getMessage());
        }
    }
}

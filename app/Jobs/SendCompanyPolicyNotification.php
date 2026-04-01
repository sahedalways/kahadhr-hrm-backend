<?php

namespace App\Jobs;

use App\Models\CompanyPolicy;
use App\Models\EmailSetting;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCompanyPolicyNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $employeeId;
    protected int $policyId;

    public function __construct(int $employeeId, int $policyId)
    {
        $this->employeeId = $employeeId;
        $this->policyId = $policyId;
    }

    public function handle()
    {
        $employee = Employee::find($this->employeeId);
        $policy = CompanyPolicy::with('company')->find($this->policyId);

        if (!$employee || !$policy) return;

        try {
            $gateway = EmailSetting::where('company_id', $employee->company_id)->first();

            if ($gateway) {
                configureSmtp($gateway);

                Mail::send('mail.company_policy_notification', [
                    'policy' => $policy,
                    'employee' => $employee,
                ], function ($message) use ($employee, $policy) {
                    $message->to($employee->email, $employee->full_name)
                        ->subject('New Company Policy: ' . $policy->title);
                });
            }
        } catch (\Exception $e) {
            Log::error("Company Policy Notification email failed for user {$employee->email}: " . $e->getMessage());
        }
    }
}

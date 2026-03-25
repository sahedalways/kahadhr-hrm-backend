<?php

namespace App\Jobs;

use App\Mail\DocumentNotificationMail;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmployeeDocumentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $employeeId;
    protected int $docTypeId;
    protected string $type;
    protected string $message;

    public function __construct(int $employeeId, int $docTypeId, string $type, string $message)
    {
        $this->employeeId = $employeeId;
        $this->docTypeId = $docTypeId;
        $this->type = $type;
        $this->message = $message;
    }

    public function handle(): void
    {
        $emp = Employee::with('user', 'company')->find($this->employeeId);
        $docType = DocumentType::find($this->docTypeId);

        if (!$emp || !$emp->user || !$docType) {
            Log::warning("Employee, user, or document type not found", [
                'employee_id' => $this->employeeId,
                'doc_type_id' => $this->docTypeId,
            ]);
            return;
        }

        $company = $emp->company;

        $gateway = EmailSetting::where('company_id', $company->id)->first();
        if (!$gateway) {
            Log::warning("Email API not configured for company {$company->id}");
            return;
        }


        try {

            configureSmtp($gateway);

            Mail::to($emp->user->email)->send(
                new DocumentNotificationMail($emp, $docType, $company, $this->message)
            );

            Log::info("Document notification email sent to {$emp->user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send document notification email", [
                'employee_id' => $emp->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

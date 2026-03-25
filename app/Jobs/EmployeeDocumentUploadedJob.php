<?php

namespace App\Jobs;

use App\Models\EmpDocument; // assuming your model is EmpDocument
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmployeeDocumentUploadedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected EmpDocument $document;

    /**
     * Pass the entire document model to the job
     */
    public function __construct(EmpDocument $document)
    {
        $this->document = $document;
    }

    public function handle(): void
    {
        $document = $this->document;

        if (!$document->employee || !$document->employee->user || !$document->documentType) {
            Log::warning("Document or employee relations missing for assignment email", [
                'document_id' => $document->id
            ]);
            return;
        }

        $employee = $document->employee;
        $docType = $document->documentType;
        $company = $employee->company;

        $gateway = EmailSetting::where('company_id', $company->id)->first();

        if (!$gateway) {
            Log::warning("Email API not configured for company {$company->id}");
            return;
        }

        try {
            configureSmtp($gateway);

            Mail::send('mail.document_uploaded', [
                'employee' => $employee,
                'document' => $document,
                'docType'  => $docType,
                'company'  => $company,
            ], function ($m) use ($employee) {
                $m->to($employee->user->email)
                    ->subject('New Document Uploaded For You');
            });

            Log::info("Document assigned email sent to {$employee->user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send document uploaded email", [
                'document_id' => $document->id,
                'employee_id' => $employee->id,
                'error'       => $e->getMessage()
            ]);
        }
    }
}

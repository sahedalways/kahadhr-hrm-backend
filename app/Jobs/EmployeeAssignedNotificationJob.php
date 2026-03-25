<?php

namespace App\Jobs;

use App\Models\CompanyDocument;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmployeeAssignedNotificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected int $documentId;

  public function __construct(int $documentId)
  {
    $this->documentId = $documentId;
  }

  public function handle(): void
  {
    $document = CompanyDocument::with('employee', 'company')->find($this->documentId);


    $employee = $document->employee;
    $company = $document->company;

    Log::info("Document loaded successfully", [
      'document_id' => $document->id,
      'employee_id' => $employee->id,
      'company_id' => $company->id
    ]);

    try {
      $gateway = EmailSetting::where('company_id', $company->id)->first();

      configureSmtp($gateway);

      // Test email content
      Mail::send('mail.employee_assigned', [
        'document' => $document,
        'employee' => $employee,
        'company'  => $company,
      ], function ($message) use ($employee) {
        $message->to($employee->email)
          ->subject('New Document Assigned to You');
      });

      Log::info("Assignment email sent to {$employee->email}");
    } catch (\Exception $e) {
      Log::error("Failed to send assignment email: " . $e->getMessage(), [
        'document_id' => $this->documentId,
        'employee_id' => $employee->id ?? null,
      ]);
    }
  }
}

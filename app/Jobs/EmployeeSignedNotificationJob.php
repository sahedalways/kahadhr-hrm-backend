<?php

namespace App\Jobs;


use App\Models\CompanyDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmployeeSignedNotificationJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $documentId;

  /**
   * Create a new job instance.
   */
  public function __construct(int $documentId)
  {
    $this->documentId = $documentId;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $document = CompanyDocument::with('employee', 'company')->find($this->documentId);
    if (!$document) {
      \Log::error("Document #{$this->documentId} not found.");
      return;
    }

    $company = $document->company;
    if (!$company) {
      \Log::error("Company not found for Document #{$this->documentId}.");
      return;
    }


    try {
      Mail::send('mail.employee_signed', [
        'document' => $document,
        'employee' => $document->employee,
        'company' => $company,
      ], function ($message) use ($company) {
        $message->to($company->company_email)
          ->subject('Document Signed by Employee');
      });

      \Log::info("Employee signed notification sent to {$company->company_email}");
    } catch (\Exception $e) {
      \Log::error("Failed to send employee signed email: " . $e->getMessage());
    }
  }
}

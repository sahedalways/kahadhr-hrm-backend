<?php

namespace App\Jobs;

use App\Models\CompanyDocument;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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

    if (!$document || !$document->employee) return;

    $employee = $document->employee;
    $company = $document->company;
    $settings = EmailSetting::first();

    if (!$settings) return;


    config([
      'mail.mailers.smtp.transport' => 'smtp',
      'mail.mailers.smtp.host' => $settings->mail_host,
      'mail.mailers.smtp.port' => $settings->mail_port,
      'mail.mailers.smtp.encryption' => $settings->mail_encryption,
      'mail.mailers.smtp.username' => $settings->mail_username,
      'mail.mailers.smtp.password' => $settings->mail_password,
      'mail.from.address' => $settings->mail_from_address,
      'mail.from.name' => $settings->mail_from_name,
    ]);

    try {
      Mail::send('mail.employee_assigned', [
        'document' => $document,
        'employee' => $employee,
        'company'  => $company,
      ], function ($message) use ($employee) {
        $message->to($employee->email)
          ->subject('New Document Assigned to You');
      });
    } catch (\Exception $e) {
      \Log::error("Failed to send assignment email: " . $e->getMessage());
    }
  }
}

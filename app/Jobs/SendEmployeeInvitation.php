<?php

namespace App\Jobs;

use App\Mail\EmployeeInvitationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmployeeInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $employee;
    public $inviteUrl;

    public function __construct($employee, $inviteUrl)
    {
        $this->employee = $employee;
        $this->inviteUrl = $inviteUrl;
    }

    public function handle()
    {
        try {
            Mail::to($this->employee->email)
                ->send(new EmployeeInvitationMail($this->employee, $this->inviteUrl));
        } catch (\Exception $e) {

            Log::error('Failed to send employee invitation email', [
                'employee_id' => $this->employee->id,
                'email' => $this->employee->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

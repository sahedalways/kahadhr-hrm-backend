<?php

namespace App\Jobs;

use App\Mail\EmployeeInvitationMail;
use App\Models\EmailSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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
        Mail::to($this->employee->email)
            ->send(new EmployeeInvitationMail($this->employee, $this->inviteUrl));
    }
}

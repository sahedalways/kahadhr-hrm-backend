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
        $settings = EmailSetting::first();

        if (!$settings) {
            \Log::error("No Email settings found. Cannot send OTP.");
            return;
        }

        \Log::info("Employee Invitation Link for {$this->employee->email}: {$this->inviteUrl}");

        // Dynamic mail config
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings->mail_host);
        Config::set('mail.mailers.smtp.port', $settings->mail_port);
        Config::set('mail.mailers.smtp.username', $settings->mail_username);
        Config::set('mail.mailers.smtp.password', $settings->mail_password);
        Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption);

        Config::set('mail.from.address', $settings->mail_from_address);
        Config::set('mail.from.name', $settings->mail_from_name);

        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
        Mail::flushMacros();


        Mail::to($this->employee->email)
            ->send(new EmployeeInvitationMail($this->employee, $this->inviteUrl));
    }
}

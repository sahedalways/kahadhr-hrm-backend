<?php

namespace App\Jobs;

use App\Mail\SupportRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSupportRequestMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new job instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to(getSiteEmail() ?? 'support@kahadhr.com')
            ->send(new SupportRequestMail($this->contact));
    }
}

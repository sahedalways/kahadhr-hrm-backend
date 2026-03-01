<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Mail::raw('This is a queued test email from Laravel.', function ($message) {
            $message->to('ssahed65@gmail.com')
                ->subject('Laravel Queued Test Email');
        });

        logger('Queued mail sent at ' . now());
    }
}

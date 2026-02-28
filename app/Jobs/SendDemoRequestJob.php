<?php

namespace App\Jobs;

use App\Mail\DemoAutoReplyMail;
use App\Mail\DemoRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDemoRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $demoData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $demoData)
    {
        $this->demoData = $demoData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $siteEmail = getSiteEmail();

        try {
            Mail::to($siteEmail)->send(new DemoRequestMail($this->demoData));

            Mail::to($this->demoData['email'], $this->demoData['full_name'])
                ->send(new DemoAutoReplyMail($this->demoData));


            Log::info('Demo request emails sent successfully', [
                'company' => $this->demoData['company_name'],
                'email' => $this->demoData['email'],
                'reference' => $this->demoData['request_id']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send demo request emails', [
                'demo_data' => $this->demoData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendDemoRequestJob failed permanently', [
            'demo_data' => $this->demoData,
            'error' => $exception->getMessage()
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Mail\ContactMessageMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContactMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Contact $contact;

    /**
     * Create a new job instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $siteEmail = getSiteEmail();

        try {
            Mail::to($siteEmail)->send(new ContactMessageMail($this->contact));
        } catch (\Exception $e) {

            Log::error('Failed to send contact message email', [
                'contact_id' => $this->contact->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

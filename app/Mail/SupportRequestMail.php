<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new message instance.
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('New Support Request')
            ->view('mail.support-request')
            ->with(['contact' => $this->contact]);

        if (!empty($this->contact->attachment_path)) {
            $email->attach(storage_path('app/support/' . $this->contact->attachment_path));
        }

        return $email;
    }
}

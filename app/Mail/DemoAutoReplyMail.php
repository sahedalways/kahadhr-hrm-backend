<?php
// app/Mail/DemoAutoReplyMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $demoData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $demoData)
    {
        $this->demoData = $demoData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Demo Request - ' . siteSetting()->site_title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.demo-auto-reply',
            with: [
                'data' => $this->demoData
            ]
        );
    }
}

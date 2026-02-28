<?php
// app/Mail/DemoRequestMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoRequestMail extends Mailable
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
            subject: '🎯 New Demo Request - ' . $this->demoData['company_name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.demo-request',
            with: [
                'data' => $this->demoData
            ]
        );
    }
}

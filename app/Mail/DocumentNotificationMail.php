<?php


namespace App\Mail;

use Illuminate\Mail\Mailable;

class DocumentNotificationMail extends Mailable
{
    public $employee;
    public $documentType;
    public $company;
    public $messageText;

    public function __construct($employee, $documentType, $company, $messageText)
    {
        $this->employee = $employee;
        $this->documentType = $documentType;
        $this->company = $company;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('Document Notification')
            ->view('mail.document_notification');
    }
}

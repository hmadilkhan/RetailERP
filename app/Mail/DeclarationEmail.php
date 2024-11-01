<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeclarationEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $branchName;
    public $subjectTitle;
    public $declarationNumber;
    public $salesData;

    /**
     * Create a new message instance.
     */
    public function __construct($branchName,$subjectTitle,$declarationNumber,$salesData)
    {
        $this->branchName = $branchName;
        $this->subjectTitle = $subjectTitle;
        $this->declarationNumber = $declarationNumber;
        $this->salesData = $salesData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectTitle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.declartion_email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->view('emails.declartion_email')
            ->subject($this->subjectTitle)
            ->with([
                'branchName' => $this->branchName,
                'declaration' => $this->declarationNumber,
                'salesData' => $this->salesData,
            ]);
    }
}

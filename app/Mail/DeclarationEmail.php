<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
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
    public $currency;
    public $date;
    public $logo;

    /**
     * Create a new message instance.
     */
    public function __construct($branchName,$subjectTitle,$declarationNumber,$salesData,$currency,$date,$logo)
    {
        $this->branchName = $branchName;
        $this->subjectTitle = $subjectTitle;
        $this->declarationNumber = $declarationNumber;
        $this->salesData = $salesData;
        $this->currency = $currency;
        $this->date = $date;
        $this->logo = $logo;
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
        return [
            // Attachment::fromPath(asset("/storage/declarationpdfs/" . 'sales_declaration_report_' . $this->declarationNumber  . '.pdf'))
            Attachment::fromPath(storage_path('app/public/declarationpdfs/sales_declaration_report_'  . $this->declarationNumber  . '.pdf'))
            // Attachment::from( public_path('storage/declarationpdfs/sales_declaration_report_' .  $this->declarationNumber  . '.pdf'))
        ];
    }

    public function build()
    {
        return $this->view('emails.declartion_email')
            ->subject($this->subjectTitle)
            ->with([
                'branchName' => $this->branchName,
                'declaration' => $this->declarationNumber,
                'salesData' => $this->salesData,
                'currency' => $this->currency,
                'date' => $this->date,
                'logo' => $this->logo,
            ]);
    }
}

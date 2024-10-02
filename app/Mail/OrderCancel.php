<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancel extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $orderNumber;
    public $orderDate;
    public $orderPaymentMethod;
    public $itemsList;
    public $orderAmount;
    public $advancePaymentAmount;
    public $remainingAmount;
    public $supportEmail;
    public $supportPhoneNumber;
    public $websiteUrl;
    public $facebookUrl;
    public $instagramUrl;
    public $twitterUrl;
    public $logo;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $customer,$logo)
    {
        $this->customerName = $customer->name;
        $this->orderNumber = $order->number;
        $this->orderDate = date("d M Y",strtotime($order->date));
        $this->orderPaymentMethod = $order->payment->payment_mode;
        $this->itemsList =$order->orderdetails; // Assuming it's a list of items
        $this->orderAmount = $order->total_amount;
        $this->advancePaymentAmount = $order->orderAccount->receive_amount;
        $this->remainingAmount = $order->remaining_amount;
        $this->supportEmail = 'support@kasheesjewellery.com';
        $this->supportPhoneNumber = '+1234567890';
        $this->websiteUrl = 'https://kasheesjewellery.com';
        $this->facebookUrl = 'https://facebook.com/kashees';
        $this->instagramUrl = 'https://instagram.com/kashees';
        $this->twitterUrl = 'https://twitter.com/kashees';
        $this->logo = $logo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Canceled - Important Update on Your Kashees Jewellery Order',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_cancelled',
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
        return $this->view('emails.order_cancelled')
            ->subject('Order Canceled - Important Update on Your Kashees Jewellery Order')
            ->with([
                'customerName' => $this->customerName,
                'orderNumber' => $this->orderNumber,
                'orderDate' => $this->orderDate,
                'orderPaymentMethod' => $this->orderPaymentMethod,
                'itemsList' => $this->itemsList,
                'orderAmount' => $this->orderAmount,
                'advancePaymentAmount' => $this->advancePaymentAmount,
                'remainingAmount' => $this->remainingAmount,
                'supportEmail' => $this->supportEmail,
                'supportPhoneNumber' => $this->supportPhoneNumber,
                'websiteUrl' => $this->websiteUrl,
                'facebookUrl' => $this->facebookUrl,
                'instagramUrl' => $this->instagramUrl,
                'twitterUrl' => $this->twitterUrl,
                'logo' => $this->logo,
            ]);
    }
}

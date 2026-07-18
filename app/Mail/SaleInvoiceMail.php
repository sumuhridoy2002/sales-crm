<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SaleInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Sale $sale) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Order Invoice #' . $this->sale->id);
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>Thank you for your purchase!</h2>
                <p>Order ID: #{$this->sale->id}</p>
                <p>Total Amount: BDT {$this->sale->total_amount}</p>
                <p>We appreciate your business.</p>
            "
        );
    }
}
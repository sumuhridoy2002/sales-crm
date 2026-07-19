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
        return new Envelope(subject: 'Your Order Invoice #'.$this->sale->id);
    }

    public function content(): Content
    {
        $itemsHtml = $this->sale->items->map(function ($item) {
            $lineTotal = number_format($item->unit_price * $item->quantity, 2);

            return "<li>{$item->product->name} x {$item->quantity} = BDT {$lineTotal}</li>";
        })->implode('');

        return new Content(
            htmlString: "
                <h2>Thank you for your purchase!</h2>
                <p>Order ID: #{$this->sale->id}</p>
                <p>Branch: {$this->sale->branch->name}</p>
                <ul>{$itemsHtml}</ul>
                <p><strong>Total Amount: BDT ".number_format($this->sale->total_amount, 2)."</strong></p>
                <p>We appreciate your business.</p>
            "
        );
    }
}

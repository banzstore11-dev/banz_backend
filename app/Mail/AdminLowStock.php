<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminLowStock extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public int $threshold = 10
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Stock Alert - ' . $this->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.low-stock',
            with: [
                'product' => $this->product,
                'threshold' => $this->threshold,
            ],
        );
    }
}

<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminProductActivity extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $action // 'created', 'updated', 'deleted'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $actionLabels = [
            'created' => 'New Product Created',
            'updated' => 'Product Updated',
            'deleted' => 'Product Deleted',
        ];

        $subject = $actionLabels[$this->action] ?? 'Product Activity';

        return new Envelope(
            subject: $subject . ' - ' . $this->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.product-activity',
            with: [
                'product' => $this->product,
                'action' => $this->action,
            ],
        );
    }
}

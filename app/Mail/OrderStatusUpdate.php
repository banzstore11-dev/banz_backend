<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $oldStatus
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];

        $statusLabel = $statusLabels[$this->order->status] ?? ucfirst($this->order->status);

        return new Envelope(
            subject: 'Order Status Update - ' . $this->order->order_number . ' (' . $statusLabel . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-update',
            with: [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
            ],
        );
    }
}

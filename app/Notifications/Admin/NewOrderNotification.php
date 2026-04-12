<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order Received - #' . $this->order->order_number)
            ->greeting('Hello Admin,')
            ->line('A new order has been placed on the platform.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total: $' . number_format($this->order->total, 2))
            ->action('View Order', url('/admin/dashboard/orders/' . $this->order->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'customer_name' => $this->order->user ? $this->order->user->name : 'Guest',
            'title' => 'New Order Received',
            'message' => 'Order #' . $this->order->order_number . ' has been placed for $' . number_format($this->order->total, 2) . '.',
            'type' => 'order_received'
        ];
    }
}

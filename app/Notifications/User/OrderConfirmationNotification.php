<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->order->user ? $this->order->user->name : ($this->order->shipping_address['first_name'] ?? 'Customer');

        return (new MailMessage)
            ->subject('Order Confirmation - #' . $this->order->order_number)
            ->greeting('Hello ' . $userName . ',')
            ->line('Thank you for your order! We\'ve received it and are working on getting it to you.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Amount: $' . number_format($this->order->total, 2))
            ->action('Track Order', url('/track-order?order=' . $this->order->order_number))
            ->line('We\'ll notify you once your order has been shipped.')
            ->line('Thank you for shopping with us!');
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
            'total' => $this->order->total,
        ];
    }
}

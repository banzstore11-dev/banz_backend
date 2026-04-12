<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    protected $order;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Order $order, string $oldStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
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
        $status = ucfirst($this->order->status);

        $message = (new MailMessage)
            ->subject('Order Status Updated - #' . $this->order->order_number)
            ->greeting('Hello ' . $userName . ',')
            ->line('Your order status has been updated to: **' . $status . '**');

        if ($this->order->status === 'shipped') {
            $message->line('Great news! Your order is on its way.');
            if ($this->order->tracking_number) {
                $message->line('Tracking Number: ' . $this->order->tracking_number);
            }
        } elseif ($this->order->status === 'delivered') {
            $message->line('Your order has been delivered. We hope you enjoy your purchase!');
        }

        return $message
            ->action('View Order Details', url('/track-order?order=' . $this->order->order_number))
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
            'status' => $this->order->status,
        ];
    }
}

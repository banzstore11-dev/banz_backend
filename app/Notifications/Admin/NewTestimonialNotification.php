<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTestimonialNotification extends Notification
{
    protected $testimonial;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
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
            ->subject('New Testimonial Submitted - ' . $this->testimonial->name)
            ->greeting('Hello Admin,')
            ->line('A new testimonial has been submitted and is awaiting approval.')
            ->line('Customer Name: ' . $this->testimonial->name)
            ->line('Rating: ' . str_repeat('⭐', $this->testimonial->rating))
            ->line('Comment: "' . $this->testimonial->content . '"')
            ->action('Review Testimonial', url('/admin/dashboard/testimonials/'))
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
            'testimonial_id' => $this->testimonial->id,
            'customer_name' => $this->testimonial->name,
            'rating' => $this->testimonial->rating,
            'title' => 'New Testimonial Submitted',
            'message' => 'New review from ' . $this->testimonial->name . ' (' . $this->testimonial->rating . ' stars) is awaiting approval.',
            'type' => 'testimonial_submitted'
        ];
    }
}

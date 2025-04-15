<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrder extends Notification
{
    use Queueable;
    protected $order;
    protected $ccEmails;
    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $ccEmails = [])
    {
        $this->order = $order;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject('New Order Received')
            ->line('A new order has been placed.')
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for using our application!')
            ->salutation('Admin, Ecommerce');

        if (!empty($this->ccEmails)) {
            $mail->cc($this->ccEmails);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New order received: Order #' . $this->order->id,
            'order_id' => $this->order->id,
        ];
    }

}

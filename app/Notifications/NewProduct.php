<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProduct extends Notification
{
    use Queueable;
    protected $product;
    protected $ccEmails;
    /**
     * Create a new notification instance.
     */
    public function __construct($product, $ccEmails = [])
    {
        $this->product = $product;
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
        $mail = (new MailMessage)
            ->subject('New Product Added: ' . $this->product->name)
            ->line('A new product has been added: ' . $this->product->name)
            ->line('Check out the latest product now!')
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
            'message' => 'New product added: ' . $this->product->name,
            'product_id' => $this->product->id,
        ];
    }
}

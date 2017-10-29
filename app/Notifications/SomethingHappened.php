<?php

namespace App\Notifications;

use App\MailMessage\SubscribedMailMessage;
use App\MailMessage\SubscriberMailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SomethingHappened extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->shouldReceiveEmail()) {
            return ['mail', 'broadcast', 'database'];
        }
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @param string $unsubscribeUrl
     * @return MailMessage
     */
    public function toMail($notifiable, $unsubscribeUrl)
    {
        return (new SubscriberMailMessage($unsubscribeUrl))
            ->line('Something happened, good thing you can receive email!')
            ->action('Notification Action', route('home'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

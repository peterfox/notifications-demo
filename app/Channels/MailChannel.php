<?php

namespace App\Channels;

use App\MailMessage\SubscriberMailMessage;
use App\Repository\SubscriptionTokenRepository;
use App\Subscriber;
use App\SubscriberFactory;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Channels\MailChannel as Channel;
use Illuminate\Notifications\Notification;

class MailChannel extends Channel
{
    /**
     * @var SubscriptionTokenRepository
     */
    private $tokens;

    /**
     * Create a new mail channel instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer $mailer
     * @param  \Illuminate\Mail\Markdown $markdown
     * @param SubscriptionTokenRepository $tokens
     */
    public function __construct(
        Mailer $mailer,
        Markdown $markdown,
        SubscriptionTokenRepository $tokens
    ) {
        parent::__construct($mailer, $markdown);
        $this->tokens = $tokens;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $token = $this->tokens->create($notifiable, $notification);

        $unsubscribeUrl = route('email.unsubscribe', [$notifiable->routeNotificationFor('mail'), $token]);

        $message = $notification->toMail($notifiable, $unsubscribeUrl);

        if (! $notifiable->routeNotificationFor('mail') &&
            ! $message instanceof Mailable) {
            return;
        }

        if ($message instanceof Mailable) {
            return $message->send($this->mailer);
        }

        $this->mailer->send(
            $this->buildView($message),
            $message->data(),
            $this->messageBuilder($notifiable, $notification, $message)
        );
    }

    /**
     * Build the mail message.
     *
     * @param  \Illuminate\Mail\Message  $mailMessage
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  SubscriberMailMessage  $message
     * @return void
     */
    protected function buildMessage($mailMessage, $notifiable, $notification, $message)
    {
        parent::buildMessage($mailMessage, $notifiable, $notification, $message);

        $mailMessage
            ->getHeaders()
            ->addTextHeader('List-Unsubscribe', "<{$message->unsubscribeUrl}>");
    }
}
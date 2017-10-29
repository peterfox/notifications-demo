<?php

namespace App\MailMessage;

use Illuminate\Notifications\Messages\MailMessage;

class SubscriberMailMessage extends MailMessage
{
    /**
     * @var string
     */
    public $unsubscribeUrl;

    public function __construct($unsubscribeUrl)
    {
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    /**
     * Get the data array for the mail message.
     *
     * @return array
     */
    public function data()
    {
        return array_merge(parent::data(), ['unsubscribeUrl' => $this->unsubscribeUrl]);
    }
}
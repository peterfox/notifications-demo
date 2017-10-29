<?php

namespace App;

use Illuminate\Notifications\Notifiable;

/**
 * Trait CanReceiveEmails
 * @package App
 *
 * @property boolean $is_email_verified
 * @property boolean $is_email_subscribed
 */
trait CanReceiveEmails
{
    use Notifiable;

    public function markVerified()
    {
        $this->email_verified_at = now();
        $this->save();
        return $this;
    }

    public function markUnverified()
    {
        $this->email_verified_at = null;
        $this->save();
        return $this;
    }

    public function unsubscribeFromNotifications()
    {
        $this->email_subscribed_at = null;
        $this->save();
        return $this;
    }

    public function subscribeToNotifications()
    {
        $this->email_subscribed_at = now();
        $this->save();
        return $this;
    }

    public function shouldReceiveEmail()
    {
        return $this->is_email_verified && $this->is_email_subscribed;
    }

    public function getIsEmailVerifiedAttribute($value)
    {
        return (boolean)$this->email_verified_at;
    }

    public function getIsEmailSubscribedAttribute($value)
    {
        return (boolean)$this->email_subscribed_at;
    }
}
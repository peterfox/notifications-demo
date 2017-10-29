<?php

namespace App\Listeners;

use App\Events\EmailNeedsVerification;
use App\Notifications\VerifyEmailAddress;
use App\Repository\VerifiedTokenRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationEmail
{
    /**
     * @var VerifiedTokenRepository
     */
    private $tokens;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(VerifiedTokenRepository $tokens)
    {
        //
        $this->tokens = $tokens;
    }

    /**
     * Handle the event.
     *
     * @param  EmailNeedsVerification  $event
     * @return void
     */
    public function handle(EmailNeedsVerification $event)
    {
        $event->user->notify(
            new VerifyEmailAddress($this->tokens->create($event->user))
        );
    }
}

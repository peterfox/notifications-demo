<?php

namespace App\Http\Controllers\Email;

use App\Repository\SubscriptionTokenRepository;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Psr\Log\LoggerInterface;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionTokenRepository
     */
    private $tokens;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(SubscriptionTokenRepository $tokens, LoggerInterface $logger)
    {
        $this->tokens = $tokens;
        $this->logger = $logger;
    }

    public function showUnsubscribeStatus()
    {
        return view('email.unsubscribe');
    }

    public function subscribe(Request $request)
    {
        $request->validate(['subscribe' => 'boolean']);

        /** @var User $user */
        $user = $request->user();

        if ($request->subscribe) {
            $user->subscribeToNotifications();
        } else {
            $user->unsubscribeFromNotifications();
        }

        return redirect()
            ->back()
            ->with(
                'status',
                $request->subscribe ?
                    'You have been subscribed to notifications.' :
                    'You have been unsubscribed from notifications.'
            );
    }

    public function unsubscribe($email, $token)
    {
        /** @var User $user */
        $user = User::whereEmail($email)->get()->first();

        if ($user && $this->tokens->exists($user, $token)) {
            $notification = $this->tokens->notificationType($user, $token);

            $user->unsubscribeFromNotifications();

            $this->logger->info("User email has been unsubscribed", [
                'email' => $user->email,
                'notification' => $notification,
            ]);

            return redirect()
                ->route('email.unsubscribed')
                ->with('status', 'Your email address will no longer receive notifications.');
        }

        return redirect()
            ->route('email.unsubscribed')
            ->with('error', 'Sorry, the unsubscribe link you used appears to be invalid.');
    }
}

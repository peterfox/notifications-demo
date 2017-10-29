<?php

namespace App\Http\Controllers\Email;

use App\Repository\VerifiedTokenRepository;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Psr\Log\LoggerInterface;

class VerificationController extends Controller
{
    /**
     * @var VerifiedTokenRepository
     */
    private $tokens;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(VerifiedTokenRepository $tokens, LoggerInterface $logger)
    {
        $this->tokens = $tokens;
        $this->logger = $logger;
    }

    public function showVerificationStatus()
    {
        return view('email.verified')
            ->with('success', true);
    }

    public function verify($email, $token)
    {
        /** @var User $user */
        $user = User::whereEmail($email)->get()->first();

        if ($user && $this->tokens->exists($user, $token)) {
            $user->markVerified();

            $this->logger->info("User email address has been verified", ['email' => $user->email]);

            return redirect()
                ->route('email.verified')
                ->with('status', 'Your email address is now verified.');
        }

        return redirect()
            ->route('email.verified')
            ->with('error', 'Your email address could not be verified.');
    }
}

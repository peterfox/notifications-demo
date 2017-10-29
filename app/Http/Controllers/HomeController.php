<?php

namespace App\Http\Controllers;

use App\Events\EmailNeedsVerification;
use App\Notifications\SomethingHappened;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('home')
            ->with('user', $request->user());
    }

    public function handleEmailChange(Request $request)
    {
        $request->validate(['email' => 'required|string|email|max:255|unique:users']);

        /** @var User $user */
        $user = $request->user();
        $user->email = $request->email;
        $user->markUnverified();

        event(new EmailNeedsVerification($user));

        return redirect()
            ->back()
            ->with('status', 'Your email address was successfully changed. You will receive a new verification email.');
    }

    public function verify(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        event(new EmailNeedsVerification($user));

        return redirect()
            ->back()
            ->with('status', "A new verification email has been to {$user->email}.");
    }

    public function test(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->notify(
            new SomethingHappened()
        );

        return redirect()
            ->back()
            ->with('status', 'A notification has been fired.');
    }
}

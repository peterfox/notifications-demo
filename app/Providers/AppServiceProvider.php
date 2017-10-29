<?php

namespace App\Providers;

use App\Repository\SubscriptionTokenRepository;
use App\Repository\VerifiedTokenRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VerifiedTokenRepository::class, function() {
            $key = config('app.key');

            if (str_start($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            return new VerifiedTokenRepository(
                app(ConnectionInterface::class),
                app(Hasher::class),
                'email_verifications',
                $key
            );
        });

        $this->app->bind(SubscriptionTokenRepository::class, function() {
            $key = config('app.key');

            if (str_start($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            return new SubscriptionTokenRepository(
                app(ConnectionInterface::class),
                app(Hasher::class),
                'email_unsubscribes',
                $key
            );
        });

        $this->app->bind(\Illuminate\Notifications\Channels\MailChannel::class, \App\Channels\MailChannel::class);
    }
}

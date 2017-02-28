<?php

namespace App\Providers;

use Pusher;
use App\CityCard\TwilioSMS;
use Illuminate\Support\ServiceProvider;
use App\CityCard\Pusher\ClerkPusherChannel;
use App\CityCard\Contracts\MessagingInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(MessagingInterface::class, TwilioSMS::class);

        $this->app->when(ClerkPusherChannel::class)
            ->needs(Pusher::class)
            ->give(function () {
                $pusher = config('broadcasting.connections.pusher_clerk');

                return new Pusher(
                    $pusher['key'],
                    $pusher['secret'],
                    $pusher['app_id']
                );
            });        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
    }
}

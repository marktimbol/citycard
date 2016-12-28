<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\User\UserRegistered' => [
            'App\Listeners\User\SendWelcomeEmail',
            'App\Listeners\User\SendEmailVerification',
            'App\Listeners\User\SendSixDigitMobileVerification',
            // 'App\Listeners\User\RegisterUserInQuickBlox',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

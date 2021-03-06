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
            // 'App\Listeners\User\SendMobileVerification',
            // 'App\Listeners\User\RegisterUserInQuickBlox',
        ],
        
        'App\Events\User\UserInvitesAFriend' => [
            'App\Listeners\User\SendInvitation',
        ],      

        'App\Events\User\UserReservedAnItem' => [
            'App\Listeners\Reservation\SendPushNotificationToOutletReservationManagers',
            // 'App\Listeners\Reservation\SendReservationInformationToOutletReservationManagers',
            // 'App\Listeners\Reservation\SendReservationInformationToUser',
        ],

        'App\Events\Reservation\ReservationWasConfirmed' => [
            'App\Listeners\Reservation\SendPushNotificationToUser',
            'App\Listeners\Reservation\SendEmailConfirmedReservation',
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

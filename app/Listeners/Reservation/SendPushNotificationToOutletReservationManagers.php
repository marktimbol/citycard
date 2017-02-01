<?php

namespace App\Listeners\Reservation;

use App\Events\User\UserReservedAnItem;
use App\Notifications\Clerk\UserReservationInformation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPushNotificationToOutletReservationManagers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserReservedAnItem  $event
     * @return void
     */
    public function handle(UserReservedAnItem $event)
    {
        foreach( $event->outlet->clerks as $clerk )
        { 
            $clerk->notify(new UserReservationInformation($event->reservation));
        }
    }
}

<?php

namespace App\Listeners\Reservation;

use App\Events\Reservation\ReservationWasConfirmed;
use App\Notifications\User\ConfirmedReservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPushNotificationToUser
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
     * @param  ReservationWasConfirmed  $event
     * @return void
     */
    public function handle(ReservationWasConfirmed $event)
    {
        $reservation = $event->reservation->load('item', 'user');
        $user = $reservation->user;

        $user->notify(new ConfirmedReservation($reservation));
        
    }
}

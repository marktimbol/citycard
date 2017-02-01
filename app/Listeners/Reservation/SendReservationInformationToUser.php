<?php

namespace App\Listeners\Reservation;

use App\Events\User\UserReservedAnItem;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReservationInformationToUser
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
        //
    }
}

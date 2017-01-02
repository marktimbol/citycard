<?php

namespace App\Listener\Reservation;

use App\Mail\ReservationConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\Reservation\ReservationWasConfirmed;

class SendEmailConfirmedReservation implements ShouldQueue
{
    use InteractsWithQueue;

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

        Mail::to($reservation->user->email)->send( new ReservationConfirmation($reservation) );
    }
}

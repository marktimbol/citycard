<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Point;
use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Reservation\ReservationWasConfirmed;

class ConfirmUserReservationController extends Controller
{
	/**
	 * Confirm user's reservation
	 */
    public function update(Outlet $outlet, Reservation $reservation)
    {
        $reservation->load('user');
        
        $reservation->confirmed = true;
    	$reservation->status = 'confirmed';
    	$reservation->save();

        $reservation->user->givePoints(
            Point::first()->reservation,
            sprintf('You received %s points because your reservation was approved.', Point::first()->reservation)
        );

		event( new ReservationWasConfirmed($outlet, $reservation) );

        return response()->json([
            'status'    => 1,
            'message'   => 'The reservation has been successfully confirmed',
            'data'  => [
                'reservation'   => $reservation,
            ]
        ]);
    }
}

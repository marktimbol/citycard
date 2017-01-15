<?php

namespace App\Http\Controllers\Api\Clerk;

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
    	$reservation->confirmed = true;
    	$reservation->save();

		event( new ReservationWasConfirmed($reservation) );

    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'The reservation has been successfully confirmed.'
    	]);
    }
}

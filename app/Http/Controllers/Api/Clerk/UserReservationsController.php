<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ReservationTransformer;

class UserReservationsController extends Controller
{
	/**
	 * Modify user's reservation
	 */
    public function update(Request $request, Outlet $outlet, Reservation $reservation)
    {
    	$reservation->confirmed = false;
        $reservation->status = 'modified';
    	$reservation->save();

        $reservation->update($request->all());
    	
        return response()->json([
            'status'    => 1,
            'message'   => 'The reservation has been successfully updated.',
            'data'  => [
                'reservation'   => ReservationTransformer::transform($reservation)
            ]
        ]);
    }

    /**
     * Cancels user reservation
     */
    public function destroy(Request $request, Outlet $outlet, Reservation $reservation)
    {    	
        $reservation->confirmed = false;
        $reservation->status = 'cancelled';
        $reservation->save();

        $outlet->cancelledReservations()->attach($reservation, [
        	'message'	=> $request->has('message') ? $request->message : ''
        ]);

        return response()->json([
            'status'    => 1,
            'message'   => 'The reservation has been successfully cancelled.',
            'data'  => [
                'cancelled_reservation'   => ReservationTransformer::tranform($reservation)
            ]
        ]);
    }       
}

<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Reservation\ReservationWasConfirmed;

class UserReservationsController extends Controller
{
	/**
	 * Modify user's reservation
	 */
    public function update(Request $request, Outlet $outlet, Reservation $reservation)
    {
    	$reservation->confirmed = false;
    	$reservation->save();

        $reservation->update($request->all());
    	
    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'The reservation has been successfully updated.'
    	]);
    }

    /**
     * Cancels user reservation
     */
    public function destroy(Request $request, Outlet $outlet, Reservation $reservation)
    {    	
        $reservation->confirmed = false;
        $reservation->save();

        $outlet->cancelledReservations()->attach($reservation, [
        	'message'	=> $request->has('message') ? $request->message : ''
        ]);

        return response()->json([
            'success'   => true,
            'message'	=> 'The reservation has been successfully cancelled.'
        ]);
    }       
}

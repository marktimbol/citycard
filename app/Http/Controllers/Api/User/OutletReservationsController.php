<?php

namespace App\Http\Controllers\Api\User;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ItemsForReservationTransformer;

class OutletReservationsController extends Controller
{
    /**
     * User makes a reservation on the outlet
     */
    public function store(Request $request, Outlet $outlet)
    {
    	$user = auth()->guard('user_api')->user();

        $request['status'] = 'pending';
    	$reservation = $user->reservations()->create($request->all());

    	$outlet->reservations()->attach($reservation->id);

    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'You have successfully submit your reservation.'
    	]);
    }

    /**
     * User modifies his/her reservation
     */
    public function update(Request $request, Outlet $outlet, Reservation $reservation)
    {        
        $reservation->confirmed = false;
        $reservation->status = 'pending';
        $reservation->save();

        $reservation->update($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'You have successfully updated your reservation.'
        ]);        
    }

    /**
     * User cancels his/her reservation
     */
    public function destroy(Outlet $outlet, Reservation $reservation)
    {
        $reservation->confirmed = false;
        $reservation->status = 'cancelled';
        $reservation->save();

        $outlet->cancelledReservations()->attach($reservation);

        return response()->json([
            'success'   => true
        ]);
    }    
}

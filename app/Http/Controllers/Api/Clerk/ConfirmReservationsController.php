<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Reservation\ReservationWasConfirmed;

class ConfirmReservationsController extends Controller
{
    public function update(Request $request, Reservation $reservation)
    {
    	$reservation->confirmed = true;
    	$reservation->save();

    	// send email notification to user
    	event(new ReservationWasConfirmed($reservation));
    	
    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'The reservation has been successfully confirmed.'
    	]);
    }
}

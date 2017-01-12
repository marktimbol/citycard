<?php

namespace App\Http\Controllers\Api\User;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CancelReservationsController extends Controller
{
    public function destroy(Outlet $outlet, Reservation $reservation)
    {
    	$reservation->confirmed = false;
    	$reservation->save();

    	$outlet->cancelledReservations()->attach($reservation);

    	return response()->json([
    		'success'	=> true
    	]);
    }
}

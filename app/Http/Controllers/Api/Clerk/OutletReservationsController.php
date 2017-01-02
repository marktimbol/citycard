<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ReservationTransformer;

class OutletReservationsController extends Controller
{
    public function index(Outlet $outlet)
    {    	
    	$outlet->load('reservations.item', 'reservations.user');

    	return ReservationTransformer::transform($outlet->reservations);
    }

    public function show(Outlet $outlet, Reservation $reservation)
    {    	
    	$reservation->load('item', 'user');
    	
    	return ReservationTransformer::transform($reservation);  	
    }
}

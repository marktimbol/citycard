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
        $reservations = $outlet->reservations()->confirmed()->get();      
        if( request()->has('show') && request()->show == 'pending' )
        {
            $reservations = $outlet->reservations()->pending()->get();      
        }  

        $reservations->load('item', 'user');

    	return ReservationTransformer::transform($reservations);
    }

    public function show(Outlet $outlet, Reservation $reservation)
    {    	
    	$reservation->load('item', 'user');
    	
    	return ReservationTransformer::transform($reservation);  	
    }
}

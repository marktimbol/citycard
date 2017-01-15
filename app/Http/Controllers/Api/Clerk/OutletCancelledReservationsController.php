<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ReservationTransformer;

class OutletCancelledReservationsController extends Controller
{
	public function index(Outlet $outlet)
	{
        $reservations = $outlet->cancelledReservations;
        $reservations->load('item', 'user');

    	return ReservationTransformer::transform($reservations);		
	}
}

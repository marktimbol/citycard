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

        return response()->json([
        	'success'	=> 1,
        	'message'	=> sprintf('%s cancelled reservations', $outlet->name),
        	'data'	=> [
        		'reservations'	=> ReservationTransformer::transform($reservations)
        	]
        ]);
	}
}

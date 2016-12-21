<?php

namespace App\Http\Controllers\Api\User;

use App\Outlet;
use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletReservationsController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$user = auth()->guard('user_api')->user();

    	$reservation = $user->reservations()->create($request->all());

    	$outlet->reservations()->attach($reservation);
    }
}

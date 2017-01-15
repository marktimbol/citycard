<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationsController extends Controller
{
    public function index()
    {    	    	
    	$user = auth()->guard('user_api')->user();
    	$user->load('reservations');

    	$userReservations = $user->reservations()->where('confirmed', true)->get();
    	
    	if( request()->has('show') && request()->show == 'pending' )
    	{
	    	$userReservations = $user->reservations()->where('confirmed', false)->get();
    	}
    
    	$userReservations->load('item');

    	return $userReservations;
    }
}

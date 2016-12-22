<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReservationsController extends Controller
{
    public function index()
    {
    	$user = auth()->guard('user_api')->user();
    	$user->load('reservations.item');
    	$userReservations = $user->reservations;

    	return $userReservations;
    }
}

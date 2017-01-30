<?php

namespace App\Http\Controllers\Api\User;

use App\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ReservationTransformer;

class UserReservationsController extends Controller
{
    public function index()
    {               
        $user = auth()->guard('user_api')->user();
        $user->load('reservations');

        $userReservations = $user->reservations()->confirmed()->get();

        if( request()->has('show') && request()->show == 'pending' )
        {
            $userReservations = $user->reservations()->pending()->get();
        }

        $userReservations->load('item');

        return $userReservations;
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('item', 'outlets');        
        return ReservationTransformer::transform($reservation);
    }
}
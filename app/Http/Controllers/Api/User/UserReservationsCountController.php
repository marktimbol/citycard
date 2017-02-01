<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserReservationsCountController extends Controller
{
	public function index()
	{
    	$user = auth()->guard('user_api')->user();

    	return response()->json([
    		'pending_reservations'	=> $user->reservations()->pending()->count(),
    		'confirmed_reservations'	=> $user->reservations()->confirmed()->count(),
    		'cancelled_reservations'	=> $user->reservations()->cancelled()->count()
    	]);		
	}
}

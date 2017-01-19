<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPendingReservationsCountController extends Controller
{
    public function index()
    {
    	$user = auth()->guard('user_api')->user();

    	return response()->json([
    		'count'	=> $user->reservations()->pending()->count()
    	]);
    }
}

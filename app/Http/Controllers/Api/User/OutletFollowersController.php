<?php

namespace App\Http\Controllers\Api\User;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletFollowersController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->load('merchant.outlets');
    	$merchant = $outlet->merchant;

    	$user = auth()->guard('user_api')->user();

    	// User follows a merchant
    	$user->merchants()->attach($merchant->id);

    	// User follows all the merchant's outlets
    	$user->outlets()->attach($merchant->outlets);

    	return response()->json([
    		'success'	=> true,
    	]);
    }
}

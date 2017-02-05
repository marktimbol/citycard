<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Outlet;
use App\Transformers\UserOutletTransformer;
use Illuminate\Http\Request;

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
        $user->follows($merchant->outlets);

    	return response()->json([
    		'success'	=> true,
            'user_outlets'  => UserOutletTransformer::transform($user->outlets),
    	]);
    }

    public function destroy(Outlet $outlet)
    {
        $user = auth()->guard('user_api')->user();

        $user->unfollow($outlet);

        return response()->json([
            'success'   => true,
            'user_outlets'  => UserOutletTransformer::transform($user->outlets),
        ]); 
    }
}

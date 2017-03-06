<?php

namespace App\Http\Controllers\Api\User;

use App\Outlet;
use App\Reward;
use App\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseRewardsController extends Controller
{
    public function store(Request $request)
    {        
        $user = auth()->guard('user_api')->user();
    	$outlet = Outlet::findOrFail($request->outlet_id);
        $reward = Reward::findOrFail($request->reward_id);

        // If this user has enough points to purchase this reward
        if( $user->points >= $reward->required_points )
        {   
            $verification_code = str_random(7);

        	$voucher = Voucher::create([
        		'reward_id'	=> $reward->id,
        		'verification_code'	=> $verification_code
        	]);

        	// Decrement the quantity of the reward
            $reward->decrement('quantity', 1);

            // Attach the voucher to the Outlet
            $outlet->vouchers()->attach($voucher);

            // Attach the voucher to the User
            $user->vouchers()->attach($voucher);

            // Decrement the points of the user
            $user->decrement('points', $reward->required_points);

        	// TODO: Send the 7-digit mobile verification to 
        	// the user's registered mobile
            
        	return response()->json([
        		'status'	=> 1,
        		'message'	=> 'You have successfully purchased the reward.',
        	]);
        }

        return response()->json([
            'status'    => 0,
            'message'   => 'You do not have enough points to purchase this reward.',
        ]);        
    }
}

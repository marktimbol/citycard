<?php

namespace App\Http\Controllers\Api\User;

use App\Reward;
use App\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRewardsController extends Controller
{
    public function store(Request $request)
    {
    	$reward = Reward::findOrFail($request->reward_id);
    	$user = auth()->guard('user_api')->user();
    	
    	$voucher = Voucher::create([
    		'user_id'	=> $user->id,
    		'reward_id'	=> $reward->id,
    		'verification_code'	=> str_random(7)
    	]);

    	// Decrement the quantity of the reward
    	$reward->quantity = $reward->quantity - 1;
    	$reward->save();

    	// TODO: Send the 7-digit mobile verification to 
    	// the user's registered mobile

    	return response()->json([
    		'status'	=> 1,
    		'message'	=> 'You have successfully purchased the reward.',
    	]);
    }
}

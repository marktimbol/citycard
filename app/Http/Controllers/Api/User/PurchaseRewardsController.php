<?php

namespace App\Http\Controllers\Api\User;

use App\Reward;
use App\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseRewardsController extends Controller
{
    public function store(Request $request)
    {        
        $user = auth()->guard('user_api')->user();
        $reward = Reward::with('outlets:id,merchant_id,name')->findOrFail($request->reward_id);

        // If this user has enough points to purchase this reward
        if( $user->getAvailablePoints() >= $reward->required_points )
        {   
            $verification_code = str_random(7);

        	$voucher = Voucher::create([
        		'reward_id'	=> $reward->id,
        		'verification_code'	=> $verification_code
        	]);

        	// Decrement the quantity of the reward
            $reward->decrement('quantity', 1);

            // Make the voucher available on the selected outlets
            $voucher->outlets()->sync($reward->outlets->pluck('id'));

            // Attach the voucher to the User
            $user->vouchers()->attach($voucher);

            // Decrement the points of the user
            $user->takePoints(
                $reward->required_points, 
                sprintf(
                    "You purchased a reward '%s' for %s points from %s.", 
                    $reward->title, $reward->required_points, 
                    $reward->outlets->first()->merchant->name)
            );

        	// TODO: Send the 7-digit mobile verification to 
        	// the user's registered mobile
            
        	return response()->json([
        		'status'	=> 1,
                'remaining_points'  => $user->getAvailablePoints(),
                'message'   => 'You have successfully purchased the reward.',
        	]);
        }

        return response()->json([
            'status'    => 0,
            'message'   => 'You do not have enough points to purchase this reward.',
        ]);        
    }
}

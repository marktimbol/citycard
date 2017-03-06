<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RedeemVouchersController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {
    	// Find the Voucher with the given verification code
    	$voucher = $outlet->vouchers()
    		->where('verification_code', $request->verification_code)
    		->first();

    	if( ! $voucher )
    	{
			return response()->json([
				'status'	=> 0,
				'message'	=> sprintf('The Voucher: %s does not exists.', $request->verification_code)
			]);	
    	}

    	if( ! $voucher->redeemed )
    	{
	    	$voucher->load(['reward:id,required_points', 'owners:id,name,points']);
	    	$user = $voucher->owners->first();

	    	$voucher->redeemed = true;
	    	$voucher->save();

			return response()->json([
				'status'	=> 1,
				'message'	=> 'The Voucher has been successfully redeemed.'
			]);	    		
    	}

		return response()->json([
			'status'	=> 0,
			'message'	=> sprintf('The Voucher: %s was already used.', $request->verification_code),
		]);
    }
}

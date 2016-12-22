<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivateOutletReservationController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {
    	$outlet->has_reservation = true;
    	
    	if( $outlet->save() )
    	{
			return response()->json([
				'success'	=> true,
                'has_reservation'   => $outlet->has_reservation
			]);  		
    	}

		return response()->json([
			'success'	=> false,
            'has_reservation'   => $outlet->has_reservation
		]);
    }
}

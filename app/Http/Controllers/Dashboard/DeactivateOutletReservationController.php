<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeactivateOutletReservationController extends Controller
{
    public function destroy(Outlet $outlet)
    {
    	$outlet->has_reservation = false;
    	
    	if( $outlet->save() )
    	{
			return response()->json([
				'success'	=> true,
			]);  		
    	}

		return response()->json([
			'success'	=> false,
		]);    	
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivateOutletMessagingController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {
    	$outlet->has_messaging = true;
    	
    	if( $outlet->save() )
    	{
			return response()->json([
				'success'	=> true,
                'has_messaging'   => $outlet->has_messaging
			]);  		
    	}

		return response()->json([
			'success'	=> false,
            'has_messaging'   => $outlet->has_messaging
		]);
    }
}

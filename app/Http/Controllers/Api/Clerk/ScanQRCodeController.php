<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class ScanQRCodeController extends Controller
{
    public function show()
    {
        // Search only the members of the Outlet
        $outlet = Outlet::findOrFail(request()->outlet_id);
        if( $user = $outlet->members()->where('uuid', request()->uuid)->first() )
        {
            return response()->json([
                'status'    => 1,
                'message'   => 'Member was found.',
                'data'  => [
                    'user'  => UserTransformer::transform($user)
                ]
            ]);    
        }

    	return response()->json([
    		'status'	=> 0,
    		'message'	=> 'Member was not found.',
    		'data'	=> [
    			'user'	=> []
    		]
    	]);    	
    }
}

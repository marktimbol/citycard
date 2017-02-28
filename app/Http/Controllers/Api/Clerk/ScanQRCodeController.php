<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class ScanQRCodeController extends Controller
{
    public function show()
    {
        if( $user = User::where('uuid', request()->uuid)->first() )
        {
            return response()->json([
                'status'    => 1,
                'message'   => 'User was found.',
                'data'  => [
                    'user'  => UserTransformer::transform($user)
                ]
            ]);          
        }

    	return response()->json([
    		'status'	=> 0,
    		'message'	=> 'User does not exist on the database.',
    		'data'	=> [
    			'user'	=> []
    		]
    	]);    	
    }
}

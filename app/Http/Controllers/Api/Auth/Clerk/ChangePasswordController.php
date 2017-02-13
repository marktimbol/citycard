<?php

namespace App\Http\Controllers\Api\Auth\Clerk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class ChangePasswordController extends Controller
{
    public function update(Request $request)
    {
    	$this->validate($request, [
            'old_password'  => 'required',
            'password'  => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
    	]);

    	$user = auth()->guard('clerk_api')->user();
        
    	$credentials = [
    		'email'	=> $user->email,
    		'password'	=> $request->old_password
    	];

    	if( auth()->guard('clerk')->attempt($credentials) )
    	{
    		$user->password = bcrypt($request->password);
    		$user->save();

            return response()->json([
                'status'    => 1,
                'message'   => 'Your password has been successfully updated.',
                'data'  => [
                    'user' => UserTransformer::transform($user)
                ]
            ]);               
    	}

        return response()->json([
            'status'    => 0,
            'message'   => 'Something went wrong. Please try again.',
            'data'  => [
                'user' => []
            ]
        ]);         
    }
}

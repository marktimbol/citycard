<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
    public function update(ChangePasswordRequest $request)
    {
    	$user = auth()->guard('user_api')->user();	
    	$credentials = [
    		'email'	=> $user->email,
    		'password'	=> $request->old_password
    	];

    	if( auth()->guard('user')->attempt($credentials) )
    	{
    		$user->password = bcrypt($request->password);
    		$user->save();

    		return response()->json([
                'success'   => true,
    			'message'	=> 'Your password has been successfully updated.'
    		]);
    	}
		return response()->json([
            'success'   => false,
			'error'	=> 'Incorrect old password.'
		]);
    }
}

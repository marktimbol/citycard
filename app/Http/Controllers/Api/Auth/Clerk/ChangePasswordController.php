<?php

namespace App\Http\Controllers\Api\Auth\Clerk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
                'success'   => true,
    			'message'	=> 'Your password has been successfully updated.'
    		]);
    	}
		return response()->json([
            'success'   => false,
			'message'	=> 'Incorrect old password.'
		]);
    }
}

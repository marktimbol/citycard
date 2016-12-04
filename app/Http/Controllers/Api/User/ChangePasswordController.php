<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:user_api');
	}

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
    			'message'	=> 'Your password has been successfully updated.'
    		]);
    	}
		return response()->json([
			'error'	=> 'Incorrect old password.'
		]);
    }
}

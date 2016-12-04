<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:user_api');
	}

    public function update(UpdateUserProfileRequest $request)
    {
    	$user = auth()->guard('user_api')->user();

    	$user->update($request->all());
		return response()->json([
			'message'	=> 'Your profile has been successfully updated.'
		]);
    }
}

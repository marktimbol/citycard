<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UpdateUserProfileRequest $request)
    {
    	$user = auth()->guard('user_api')->user();
    	$user->update($request->all());

		return response()->json([
			'success'	=> true,
			'message'	=> 'Your profile has been successfully updated.'
		]);
    }
}

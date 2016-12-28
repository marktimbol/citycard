<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateEmailController extends Controller
{
    public function update(Request $request)
    {
    	$this->validate($request, [
    		'email'	=> 'required|email'
    	]);

    	$user = auth()->guard('user_api')->user();

    	$user->update([
    		'email'	=> $request->email,
    	]);

		return response()->json([
			'success'	=> true,
			'message'	=> 'Your email has been successfully updated.'
		]);
    }
}

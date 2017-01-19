<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->guard('user_api')->user();
        $user->load('photos');
        
        return UserTransformer::transform($user);
    }

    public function update(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$user = auth()->guard('user_api')->user();

    	$user->update([
    		'name'	=> $request->name,
    	]);

		return response()->json([
			'success'	=> true,
			'message'	=> 'Your profile has been successfully updated.'
		]);
    }
}

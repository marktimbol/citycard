<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPhotosController extends Controller
{
    public function store(Request $request)
    {
    	$user = auth()->guard('user_api')->user();
	    $uploadPath = sprintf('users/%s', $user->id);

	    if( $file = $request->file->store($uploadPath, 's3') )
	    {
	    	$photo = $user->photos()->create([
	    		'url'	=> $file
	    	]);
	    	
	    	return response()->json([
	    		'uploaded'	=> true,
	    		'photo'	=> $photo
	    	]);	    	
	    }

    	return response()->json([
    		'uploaded'	=> false
    	]);
    }
}

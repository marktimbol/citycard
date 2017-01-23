<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserQRCodeController extends Controller
{
    public function store(Request $request)
    {
    	$user = auth()->guard('user_api')->user();
    	$user->load('qrcode');

	    $uploadPath = sprintf('users/%s/qrcode', $user->id);

	    if( $file = $request->file->store($uploadPath, 's3') )
	    {
	    	// User has existing QR Code
	    	if( ! empty($user->qrcode) )
	    	{
	    		// Delete the photo from S3
	    		Storage::delete($user->qrcode->photo);

	    		$photo = $user->qrcode()->update([
	    			'photo'	=> $file
	    		]);

	    	} else {    		
		    	
		    	$photo = $user->qrcode()->create([
		    		'photo'	=> $file
		    	]);
	    	}

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
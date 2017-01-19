<?php

namespace App\Http\Controllers\Api\User;

use App\Photo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPhotosController extends Controller
{
    public function store(Request $request)
    {
    	$user = auth()->guard('user_api')->user();
	    $uploadPath = sprintf('users/%s', $user->id);

	    if( $file = $request->file->store($uploadPath, 's3') )
	    {
	    	// User has existing photo
	    	if( $user->photos()->count() > 0 )
	    	{
	    		$photo = Photo::where('imageable_type', 'App\User')
	    			->where('imageable_id', $user->id)
	    			->get();

	    		// Delete the photo from S3
	    		Storage::delete($photo->url);

	    		$photo->url = $file;
	    		$photo->save();

	    	} else {    		
		    	
		    	$photo = $user->photos()->create([
		    		'url'	=> $file
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

<?php

namespace App\Http\Controllers\Dashboard;

use App\Clerk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClerkPhotosController extends Controller
{
    public function update(Request $request, Clerk $clerk)
    {
        $clerk->load('merchant');

        if( $clerk->merchant->photo !== '' )
        {
            deletePhoto($clerk->merchant->photo);
        }

	    $uploadPath =
            sprintf('merchants/%s/clerks/%s/photos',
                str_slug($clerk->merchant->name),
                str_slug($clerk->fullName())
            );
	    $file = $request->file->store($uploadPath, 's3');

        $clerk->photo = $file;
        $clerk->save();

    	return back();
    }
}

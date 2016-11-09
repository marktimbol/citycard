<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use App\Photo;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class OutletPhotosController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->load('merchant');

	    $uploadPath = sprintf('merchants/%s/outlets/%s/photos', 
	    				str_slug($outlet->merchant->name), 
	    				str_slug($outlet->name)
	    			);
	    $file = $request->file->store($uploadPath, 's3');

    	$outlet->photos()->create([
    		'url'	=> $file
    	]);

    	return back();
    }

    public function destroy(Outlet $outlet, Photo $photo)
    {       
        Storage::delete($photo->url);
        $photo->delete();

        return back();
    }
}

<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Album;
use App\AlbumPhoto;
use App\Http\Controllers\Controller;
use App\Outlet;
use App\Transformers\Outlet\AlbumOutletMerchantTransformer;
use App\Transformers\Outlet\AlbumPhotosTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumPhotosController extends Controller
{
    public function index(Album $album)
    {
    	$album->load('photos');
    	return AlbumPhotosTransformer::transform($album->photos);
    }

    public function store(Request $request, Album $album)
    {
    	$album->load('outlet.merchant');
    	
    	// We only want the Outlet & Merchant name to generate
    	// a name for the folder on the s3 bucket. Nothing more :)
    	$outlet = AlbumOutletMerchantTransformer::transform($album->outlet);

	    $uploadPath = sprintf(
    		'merchants/%s/outlets/%s/albums/%s/photos',
    		str_slug($outlet['merchant']['name']),
    		str_slug($outlet['name']),
    		$album->id
	    );

	    $file = $request->file->store($uploadPath, 's3');

    	$photo = $album->photos()->create([
    		'url'	=> $file
    	]);

    	return response()->json([
    		'success'	=> true,
    	]);
    }

    public function destroy(Album $album, AlbumPhoto $photo)
    {
    	$photo->delete();
    	Storage::delete($photo->url);
    
    	return response()->json([
    		'success'	=> true,
    	]);
    }
}

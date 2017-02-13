<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Album;
use App\Outlet;
use App\AlbumPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PhotosTransformer;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Outlet\AlbumPhotosTransformer;
use App\Transformers\Outlet\AlbumOutletMerchantTransformer;

class AlbumPhotosController extends Controller
{
    public function index(Album $album)
    {
    	$album->load('photos');

        return response()->json([
            'status'    => 1,
            'message'   => 'Showing album photos',
            'data'  => [
                'photos'    => AlbumPhotosTransformer::transform($album->photos)
            ]
        ]);
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
            'status'    => 1,
            'message'   => 'Photo was successfully uploaded in ' . $uploadPath,
            'data'  => [
                'photo'    => PhotosTransformer::transform($photo)
            ]
        ]);
    }

    public function destroy(Album $album, AlbumPhoto $photo)
    {
    	$photo->delete();
    	Storage::delete($photo->url);
    
        return response()->json([
            'status'    => 1,
            'message'   => 'Photo was successfully deleted.',
            'data'  => [
                'deleted_photo'    => PhotosTransformer::transform($photo)
            ]
        ]);
    }
}

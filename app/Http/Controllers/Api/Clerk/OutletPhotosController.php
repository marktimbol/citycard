<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Photo;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Outlet\OutletPhotosTransformer;

class OutletPhotosController extends Controller
{
    public function index(Outlet $outlet)
    {
        return $outlet->photos()
            ->latest()
            ->paginate(config('pagination.count'));
    }

    public function store(Request $request, Outlet $outlet)
    {
        $outlet->load('merchant');
        $uploadPath = sprintf('merchants/%s/outlets/%s/photos', str_slug($outlet->merchant->name), $outlet->id);
        
        if( $file = $request->file->store($uploadPath, 's3') )
        {
            $photo = $outlet->photos()->create([
                'url' => $file
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => 'Outlet Photo has been successfully uploaded.',
                'data'  => [
                    'photo' => OutletPhotosTransformer::transform($photo)
                ]
            ]);
        }

        return response()->json([
            'status'    => 0,
            'message'   => 'Something went wrong while uploading a photo. Please try again..',
            'data'  => []
        ]);

    }

    public function destroy(Outlet $outlet, Photo $photo)
    {
        if( Storage::delete($photo->url) )
        {
            $photo->delete();
            return response()->json([
                'status'    => 1,
                'message'   => 'Outlet Photo has been successfully deleted.',
            ]);
        }

        return response()->json([
            'status'    => 0,
            'message'   => 'Something went wrong while deleting a photo. Please try again.',
        ]);        
    }
}

<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Album;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\Outlet\AlbumTransformer;

class OutletAlbumsController extends Controller
{
    public function index(Outlet $outlet)
    {
    	$outlet->load('albums.photos');

        return response()->json([
            'status'    => 1,
            'message'   => 'Outlet Albums',
            'data'  => [
                'albums'    => AlbumTransformer::transform($outlet->albums)
            ]
        ]);
    }

    public function show(Outlet $outlet, Album $album)
    {
        return response()->json([
            'status'    => 1,
            'message'   => 'Showing an Album',
            'data'  => [
                'album'    => AlbumTransformer::transform($album)
            ]    
        ]);
    }

    public function store(Request $request, Outlet $outlet)
    {
        $outlet->load('merchant');

        $album = $outlet->albums()->create([
            'title' => $request->title
        ]);

        $album->load('photos');

        foreach( $request->photos as $photo )
        {
            try {          
                $path = sprintf(
                    'merchants/%s/outlets/%s/albums/%s/photos',
                    str_slug($outlet->merchant->name),
                    str_slug($outlet->name),
                    $album->id
                );

                $file = $photo->store($path, 's3');

                $album->photos()->create([
                    'url'   => $file
                ]);

            } catch (\Exception $e) {
                
            }
        }

        return response()->json([
            'status'    => 1,
            'message'   => 'An Outlet album has been successfully saved.',
            'data'  => [
                'album' => AlbumTransformer::transform($album)
            ]
        ]);
    }

    public function update(Request $request, Outlet $outlet, Album $album)
    {
        $album->update([
            'title' => $request->title
        ]);

        return response()->json([
            'status'    => 1,
            'message'   => 'An Outlet album has been successfully updated.',
            'data'  => [
                'album' => AlbumTransformer::transform($album)
            ]
        ]);
    }

    public function destroy(Outlet $outlet, Album $album)
    {
        $album->delete();

        return response()->json([
            'status'    => 1,
            'message'   => 'An Outlet album has been successfully deleted.',
            'data'  => [
                'deleted_album' => AlbumTransformer::transform($album)
            ]
        ]);
    }
}

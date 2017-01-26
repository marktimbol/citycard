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

    	return AlbumTransformer::transform($outlet->albums);
    }

    public function show(Outlet $outlet, Album $album)
    {
    	$album->load('photos');

    	return AlbumTransformer::transform($album);
    }

    public function store(Request $request, Outlet $outlet)
    {
        $album = $outlet->albums()->create([
            'title' => $request->title
        ]);

        return response()->json([
            'success'   => true,
        ]);
    }

    public function update(Request $request, Outlet $outlet, Album $album)
    {
        $album->update([
            'title' => $request->title
        ]);

        return response()->json([
            'success'   => true,
        ]);
    }

    public function destroy(Outlet $outlet, Album $album)
    {
        $album->delete();

        return response()->json([
            'success'   => true,
        ]);        
    }
}

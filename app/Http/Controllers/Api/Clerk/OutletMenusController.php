<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\OutletMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Outlet\OutletMenusTransformer;

class OutletMenusController extends Controller
{
    public function index(Outlet $outlet)
    {
        return response()->json([
            'status'    => 1,
            'message'   => 'Outlet Menus',
            'data'  => [
                'menus' => OutletMenusTransformer::transform($outlet->menus)
            ]
        ]);
    }

    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->load('merchant');

	    $uploadPath = sprintf('merchants/%s/outlets/%s/menus', str_slug($outlet->merchant->name), $outlet->id);
	    $file = $request->file->store($uploadPath, 's3');

    	$menu = $outlet->menus()->create([
    		'url'	=> $file
    	]);

        return response()->json([
            'status'    => 1,
            'message'   => 'Outlet Menu has been successfully created.',
            'data'  => [
                'menu' => OutletMenusTransformer::transform($menu)
            ]
        ]);        
    }

    public function destroy(Outlet $outlet, OutletMenu $menu)
    {       
        Storage::delete($menu->url);
        $menu->delete();

        return response()->json([
            'status'    => 1,
            'message'   => 'Outlet Menu has been successfully deleted.',
            'data'  => [
                'deleted_menu' => OutletMenusTransformer::transform($menu)
            ]
        ]);
    }    
}

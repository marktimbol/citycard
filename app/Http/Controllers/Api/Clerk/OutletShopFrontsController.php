<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use App\ShopFront;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Outlet\OutletShopFrontsTransformer;

class OutletShopFrontsController extends Controller
{
    public function index(Outlet $outlet)
    {
    	return OutletShopFrontsTransformer::transform($outlet->shop_fronts);
    }

    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->load('merchant');

	    $uploadPath = sprintf('merchants/%s/outlets/%s/shop_fronts', str_slug($outlet->merchant->name), $outlet->id);
	    $file = $request->file->store($uploadPath, 's3');

    	return $outlet->shop_fronts()->create([
    		'url'	=> $file
    	]);
    }

    public function destroy(Outlet $outlet, ShopFront $shop_front)
    {       
        Storage::delete($shop_front->url);
        $shop_front->delete();

        return response()->json([
        	'success'	=> true,
        ]);
    }   
}

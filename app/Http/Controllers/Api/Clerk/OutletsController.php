<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class OutletsController extends Controller
{
    public function show(Outlet $outlet)
    {
        $outlet->load(
            'clerks', 'posts.sources', 'posts.outlets:id,name',
            'posts.category', 'posts.photos',
            'itemsForReservation', 'reservations.item', 'reservations.user'            
        );    	
        
    	return OutletTransformer::transform($outlet);
    }
}

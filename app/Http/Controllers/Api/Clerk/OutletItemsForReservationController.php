<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletItemsForReservationController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$this->validate($request, [
    		'title'	=> 'required|min:3'
    	]);

    	$outlet->itemsForReservation()->create([
    		'title'	=> $request->title
    	]);

    	return response()->json([
    		'success'	=> true,
    	]);
    }
}

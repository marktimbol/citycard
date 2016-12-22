<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemsForReservationController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$this->validate($request, [
    		'title'	=> 'required'
    	]);

    	$itemForReservation = $outlet->itemsForReservation()->create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $itemForReservation->title));
    	return back();
    }
}

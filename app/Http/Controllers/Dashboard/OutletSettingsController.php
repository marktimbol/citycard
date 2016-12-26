<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletSettingsController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {    	
    	$outlet->has_reservation = $request->has_reservation;
    	$outlet->has_messaging = $request->has_messaging;
    	$outlet->has_menus = $request->has_menus;

    	$outlet->save();

    	return response()->json([
    		'success'	=> true,
    		'has_reservation'	=> $outlet->has_reservation,
    		'has_messaging'	=> $outlet->has_messaging,
    		'has_menus'	=> $outlet->has_menus,
    	]);
    }
}

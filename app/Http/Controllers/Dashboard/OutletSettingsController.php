<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletSettingsController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {
    	$outlet->has_reservation = (int) $request->has_reservation;
    	$outlet->has_messaging = (int) $request->has_messaging;
        $outlet->has_menus = (int) $request->has_menus;
    	$outlet->is_open = (int) $request->is_open;
    	$outlet->update();

    	return response()->json([
    		'success'	=> true,
    		'has_reservation'	=> $outlet->has_reservation,
    		'has_messaging'	=> $outlet->has_messaging,
            'has_menus' => $outlet->has_menus,
    		'is_open'	=> $outlet->is_open,
    	]);
    }
}

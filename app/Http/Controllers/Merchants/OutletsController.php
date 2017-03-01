<?php

namespace App\Http\Controllers\Merchants;

use App\Outlet;
use JavaScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletsController extends Controller
{
    public function index()
    {
    	return 'show all outlets';
    }

    public function show(Outlet $outlet)
    {
    	$outlet->load('merchant', 'clerks', 'itemsForReservation', 'categories', 'subcategories');

    	JavaScript::put([
    		'update_settings_route'	=> sprintf('/outlets/%s/settings', $outlet->id),
			'has_reservation'	=> $outlet->has_reservation,
			'has_messaging' => $outlet->has_messaging,
			'has_menus' => $outlet->has_menus,
			'is_open' => $outlet->is_open,   		
    	]);

    	return view('merchants.outlets.show', compact('outlet'));
    }
}

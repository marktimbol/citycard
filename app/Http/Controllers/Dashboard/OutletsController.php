<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletsController extends Controller
{
	public function show(Outlet $outlet)
	{
		$outlet->load('posts', 'clerks', 'photos', 'areas.city.country');
		
		return view('dashboard.outlets.show', compact('outlet'));
	}
}

<?php

namespace App\Http\Controllers;

use App\Outlet;
use Illuminate\Http\Request;

class OutletsController extends Controller
{
    public function show(Outlet $outlet)
    {
    	$outlet->load('posts');
    	return view('public.outlets.show', compact('outlet'));
    }
}

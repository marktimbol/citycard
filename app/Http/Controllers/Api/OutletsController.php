<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Outlet;
use Illuminate\Http\Request;

class OutletsController extends Controller
{
    public function index()
    {
    	return Outlet::latest()->get();
    }

    public function show(Outlet $outlet)
    {
    	$outlet->load('merchant', 'clerks', 'posts.photos');

    	return $outlet;
    }
}

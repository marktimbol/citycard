<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class OutletsController extends Controller
{
    public function index()
    {
    	return Outlet::latest()->get();
    }

    public function show(Outlet $outlet)
    {
    	$outlet->load('merchant', 'clerks', 'posts', 'posts.photos');

    	return OutletTransformer::transform($outlet);
    }
}

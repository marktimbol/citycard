<?php

namespace App\Http\Controllers\Api;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchOutletsController extends Controller
{
    public function index()
    {
    	$key = request()->key;

    	return Outlet::search($key)->get();
    }
}

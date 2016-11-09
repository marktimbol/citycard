<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Outlet;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class OutletPhotosController extends Controller
{
    public function index(Outlet $outlet)
    {
    	$outlet->load('photos');

    	return $outlet->photos;
    }
}

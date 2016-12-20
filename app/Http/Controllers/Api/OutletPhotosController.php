<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Outlet;
use App\Transformers\PhotoTransformer;
use Illuminate\Http\Request;

class OutletPhotosController extends Controller
{
    public function index(Outlet $outlet)
    {
    	$outlet->load('photos');

    	return PhotoTransformer::transform($outlet->photos);
    }
}

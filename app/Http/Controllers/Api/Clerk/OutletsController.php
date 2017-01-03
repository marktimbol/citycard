<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class OutletsController extends Controller
{
    public function show(Outlet $outlet)
    {
    	return OutletTransformer::transform($outlet);
    }
}

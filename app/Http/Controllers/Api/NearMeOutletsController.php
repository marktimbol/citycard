<?php

namespace App\Http\Controllers\Api;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletNearMeTransformer;

class NearMeOutletsController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with('merchant', 'categories')->latest();

        $near_outlets = $outlets->nearMe(request()->lat, request()->lng)->pluck('id');
        $outlets = $outlets->whereIn('id', $near_outlets);

        return OutletNearMeTransformer::transform($outlets->get());
    }
}

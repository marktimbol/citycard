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

        $distance = request()->has('distance') ? request()->distance : config('distance.near_outlets');
        $near_outlets = $outlets->nearMe(request()->lat, request()->lng, $distance)->pluck('id');

        $outlets = $outlets->whereIn('id', $near_outlets);

        return OutletNearMeTransformer::transform($outlets->get());
    }
}

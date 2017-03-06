<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;
use App\Transformers\OutletNearMeTransformer;

class OutletsController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with('merchant', 'categories', 'rewards.photos')->latest();

        if( request()->has('lat') && request()->has('lng') )
        {
            $near_outlets = $outlets->nearMe(request()->lat, request()->lng)->pluck('id');
            $outlets = $outlets->whereIn('id', $near_outlets);
        }

        return OutletNearMeTransformer::transform($outlets->get());
    }

    public function show(Outlet $outlet)
    {
        $outlet->load(
            'merchant.outlets', 'clerks', 'posts.sources', 
            'posts.merchant', 'posts.outlets:id,name', 
            'posts.category', 'posts.photos', 'itemsForReservation',
            'rewards.photos'
        );        

    	return OutletTransformer::transform($outlet);
    }
}

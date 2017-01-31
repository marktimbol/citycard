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
        $outlets = Outlet::with('merchant')->latest();

        if( request()->has('lat') && request()->has('lng') ) {
            $outlets = $outlets->byDistance(request()->lat, request()->lng);
            $outlet_ids = $outlets->pluck('id');

            $outlets = Outlet::with('merchant')->latest()->whereIn('id', $outlet_ids)->get();
            return OutletTransformer::transform($outlets);
        }

        return OutletTransformer::transform($outlets->get());
    }

    public function show(Outlet $outlet)
    {
        $outlet->load(
            'merchant.outlets', 'clerks', 'posts.sources', 
            'posts.merchant', 'posts.outlets:id,name', 
            'posts.category', 'posts.photos', 'itemsForReservation'
        );        

    	return OutletTransformer::transform($outlet);
    }
}

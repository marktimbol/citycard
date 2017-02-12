<?php

namespace App\Http\Controllers\Api;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class SearchOutletsController extends Controller
{
    public function index()
    {
    	$key = request()->key;

    	$results = Outlet::search($key)->get();

    	$outlets = Outlet::with(['merchant', 'categories', 'photos'])
    		->latest()
    		->whereIn('id', $results->pluck('id'))
    		->paginate(config('pagination.count'));

    	return OutletTransformer::transform($outlets->getCollection());
    }
}

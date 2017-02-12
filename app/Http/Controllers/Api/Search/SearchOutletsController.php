<?php

namespace App\Http\Controllers\Api\Search;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\SearchOutletTransformer;

class SearchOutletsController extends Controller
{
    public function index()
    {
    	$key = request()->key;

    	$results = Outlet::search($key)->get();

    	$outlets = Outlet::with(['merchant', 'photos'])
    		->latest()
    		->whereIn('id', $results->pluck('id'))
    		->paginate(config('pagination.count'));

    	return SearchOutletTransformer::transform($outlets->getCollection());
    }
}

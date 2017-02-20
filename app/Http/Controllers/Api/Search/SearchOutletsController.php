<?php

namespace App\Http\Controllers\Api\Search;

use JavaScript;
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
            ->get();

        JavaScript::put([
            // User's token to Follow/unfollow an Outlet
            'api_token' => auth()->check() ? auth()->user()->api_token : null,
            's3_bucket_url' => getS3BucketUrl(),
            'data'  => [
                'outlets'   => SearchOutletTransformer::transform($outlets),
                'posts' => [],
                'user_outlets' => auth()->check() ? auth()->user()->following_outlets() : []
            ],
        ]);

        return view('public.search.index', compact('key'));            
    }
}

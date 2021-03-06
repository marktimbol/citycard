<?php

namespace App\Http\Controllers\Api\Search;

use App\Post;
use App\Outlet;
use JavaScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserOutletTransformer;
use App\Transformers\SearchPostsTransformer;
use App\Transformers\SearchOutletTransformer;

class SearchController extends Controller
{
	public function index()
	{	
		$key = request()->key;		
	    $post_results = Post::search($key)->get();
	    $outlet_results = Outlet::search($key)->get();

	    $all = Post::with([
	    	'category', 'outlets:id,name', 'merchant',
	    	'photos:id,url,thumbnail,imageable_id,imageable_type',
	    	'sources'
	    ])
            ->whereIn('id', $post_results->pluck('id'))
            ->get();

	    $newsfeeds = Post::with([
	    	'category', 'outlets:id,name', 'merchant',
	    	'photos:id,url,thumbnail,imageable_id,imageable_type',
	    	'sources'
	    ])
            ->latest()
            ->whereIn('id', $post_results->pluck('id'))
            ->whereType('newsfeed')
            ->take(9)
            ->get();

	    $deals = Post::with([
	    	'category', 'outlets:id,name', 'merchant',
	    	'photos:id,url,thumbnail,imageable_id,imageable_type',
	    	'sources'
	    ])
            ->latest()
            ->whereIn('id', $post_results->pluck('id'))
            ->whereType('deals')
            ->take(9)
            ->get();

	    $events = Post::with([
	    	'category', 'outlets:id,name', 'merchant',
	    	'photos:id,url,thumbnail,imageable_id,imageable_type',
	    	'sources'
	    ])
            ->upcomingEvents()
            ->whereIn('id', $post_results->pluck('id'))
            ->take(9)
            ->get();

	    $outlets = Outlet::with('photos:id,url,thumbnail,imageable_id,imageable_type', 'merchant')
	    	->whereIn('id', $outlet_results->pluck('id'))
	            ->take(9)
	            ->get();

	    if( request()->wantsJson() )
	    {    	
		    return response()->json([
		    	// 'all'	=> SearchPostsTransformer::transform($all),
		    	'newsfeeds'	=> SearchPostsTransformer::transform($newsfeeds),
		    	'deals'	=> SearchPostsTransformer::transform($deals),
		    	'events' => SearchPostsTransformer::transform($events),
		    	'outlets' => SearchOutletTransformer::transform($outlets),
		    ]);
	    }

	    JavaScript::put([
            // User's token to Follow/unfollow an Outlet
            'api_token' => auth()->check() ? auth()->user()->api_token : '',
	    	's3_bucket_url'	=> getS3BucketUrl(),
	    	'data'	=> [
	    		'posts'	=> SearchPostsTransformer::transform($all),
	    		'outlets'	=> SearchOutletTransformer::transform($outlets),
	    		'events'	=> [],
	    		'deals'	=> [],
	    		'newsfeeds'	=> [],
	    		'user_outlets' => auth()->check() ? auth()->user()->following_outlets() : []
	    	],
	    ]);

	    return view('public.search.index', compact('events', 'newsfeeds', 'deals', 'outlets', 'key'));
	}
}

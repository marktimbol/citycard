<?php

namespace App\Http\Controllers\Api\Search;

use App\Post;
use App\Outlet;
use JavaScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

	    // dd(
	    // 	'events', $events->toArray(), 
	    // 	'newsfeeds', $newsfeeds->toArray(), 
	    // 	'deals', $deals->toArray(), 
	    // 	'outlets', $outlets->toArray()
	    // );

	    JavaScript::put([
	    	's3_bucket_url'	=> getS3BucketUrl(),
	    	'data'	=> [
	    		'results'	=> SearchPostsTransformer::transform($all),
	    		'outlets'	=> [],
	    		'events'	=> [],
	    		'deals'	=> [],
	    		'newsfeeds'	=> [],
	    	],
	    ]);

	    return view('public.search.index', compact('events', 'newsfeeds', 'deals', 'outlets', 'key'));
	}
}

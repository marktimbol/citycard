<?php

namespace App\Http\Controllers\Api\Search;

use App\Post;
use App\Outlet;
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

	    $all = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
	            ->whereIn('id', $post_results->pluck('id'))
	            ->get();

	    $newsfeeds = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
	            ->latest()
	            ->whereIn('id', $post_results->pluck('id'))
	            ->whereType('newsfeed')
	            ->take(10)
	            ->get();

	    $deals = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
	            ->latest()
	            ->whereIn('id', $post_results->pluck('id'))
	            ->whereType('deals')
	            ->take(10)
	            ->get();

	    $events = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
	            ->latest()
	            ->whereIn('id', $post_results->pluck('id'))
	            ->whereType('events')
	            ->take(10)
	            ->get();

	    $outlets = Outlet::with('photos', 'merchant')
	    	->whereIn('id', $outlet_results->pluck('id'))
	            ->take(10)
	            ->get();

	    $newsfeeds = SearchPostsTransformer::transform($newsfeeds);
	    $deals = SearchPostsTransformer::transform($deals);
	    $events = SearchPostsTransformer::transform($events);
	    $outlets = SearchOutletTransformer::transform($outlets);

	    if( request()->wantsJson() )
	    {    	
		    return response()->json([
		    	'newsfeeds'	=> $newsfeeds,
		    	'deals'	=> $deals,
		    	'events' => $events,
		    	'outlets' => $outlets,
		    ]);
	    }

	    return view('public.search.index', compact('newsfeeds', 'deals', 'events', 'outlets', 'key'));
	}
}

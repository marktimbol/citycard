<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;
use App\Transformers\SearchPostsTransformer;

class SearchNewsFeedsController extends Controller
{
	public function index()
	{	
		$key = request()->key;
		
	    $post_results = Post::search($key)->get();

	    $results = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
	    		->latest()
	            ->whereIn('id', $post_results->pluck('id'))
	            ->where('type', 'newsfeed')
	            ->paginate(config('pagination.count'));

	    return SearchPostsTransformer::transform($results->getCollection());
	}
}

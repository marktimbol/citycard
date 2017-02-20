<?php

namespace App\Http\Controllers\Api\Search;

use App\Post;
use JavaScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
	            ->get();

        JavaScript::put([
            // User's token to Follow/unfollow an Outlet
            'api_token' => auth()->check() ? auth()->user()->api_token : null,
            's3_bucket_url' => getS3BucketUrl(),
            'data'  => [
                'outlets'   => [],
                'user_outlets' => [],
                'posts' => SearchPostsTransformer::transform($results),
            ],
        ]);

        return view('public.search.index', compact('key'));  
	}
}

<?php

namespace App\Http\Controllers;

use App\Post;
use JavaScript;
use Illuminate\Http\Request;
use App\Transformers\PostTransformer;

class PostsController extends Controller
{
    public function index()
    {
    	$posts = Post::with('merchant', 'outlets:id,name', 'photos')->latest()->paginate(20);

    	JavaScript::put([
    		's3_bucket_url' => getS3BucketUrl()
    	]);

    	if( request()->wantsJson() ) {
    		return response()->json([
    			'next_page_url'	=> $posts->nextPageUrl(),
    			'posts'	=> PostTransformer::transform($posts->getCollection())
    		]);
    	}

    	JavaScript::put([
    		'next_page_url'	=> $posts->nextPageUrl(),
    		'posts'	=> PostTransformer::transform($posts->getCollection())
    	]);    		

    	return view('public.posts.index', compact('posts'));
    }
}

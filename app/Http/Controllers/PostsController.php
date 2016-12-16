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
    	$posts = Post::with('merchant', 'outlets:id,name', 'photos')->latest()->paginate(10);

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

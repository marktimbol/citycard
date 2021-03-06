<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\PostTransformer;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function index(Request $request)
    {
		$paginator = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->published()
					->latest()
					->paginate(config('pagination.count'));

        if( $request->has('s') )
        {
            $key = $request->s;
            $posts = Post::search($key)->get();
            $paginator = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->whereIn('id', $posts->pluck('id'))
                    ->paginate(config('pagination.count'));
        }   

    	if( $request->has('filter') && $request->filter == 'true' )
    	{
	        $paginator = Post::filterBy($request)
                        ->published()
		    			->latest()
		    			->paginate(config('pagination.count'));
    	}     

		return PostTransformer::transform($paginator->getCollection());
    }

    public function show(Post $post)
    {
        $post->load('merchant');
        
        return PostTransformer::transform($post);
    }
}

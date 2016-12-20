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
					->paginate(10);

        if( $request->has('s') )
        {
            $key = $request->s;
            $posts = Post::search($key)->get();
            $paginator = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->whereIn('id', $posts->pluck('id'))
                    ->paginate(10);
        }   

    	if( $request->has('filter') && $request->filter == 'true' )
    	{
	        $paginator = Post::filterBy($request)
                        ->published()
		    			->latest()
		    			->paginate(10);
    	}     

		return PostTransformer::transform($paginator->getCollection());
    }
}

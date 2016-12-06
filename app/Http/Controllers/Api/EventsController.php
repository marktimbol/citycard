<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class EventsController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->where('type', 'events')->paginate(10);

    	if( $request->has('filter') && $request->filter == 'true' )
    	{
	        $posts = Post::filterBy($request)
		    			->where('type', 'events')
		    			->paginate(10);
    	}

		return PostTransformer::transform($posts->getCollection());    		
    }
}

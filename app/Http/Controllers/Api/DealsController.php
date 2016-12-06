<?php

namespace App\Http\Controllers\Api;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class DealsController extends Controller
{
    public function index(Request $request)
    {
    	$posts = Post::getDeals()->paginate(10);

    	if( $request->has('filter') && $request->filter == 'true' )
    	{
	        $posts = Post::filterBy($request)
		    			->where('type', 'deals')
		    			->paginate(10);
    	}

		return PostTransformer::transform($posts->getCollection());    	
    }
}

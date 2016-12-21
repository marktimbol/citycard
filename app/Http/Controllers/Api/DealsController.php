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
    	$posts = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
    				->where('type', 'deals')
                    ->paginate(config('pagination.count'));
    				
    	if( $request->has('filter') && $request->filter == 'true' )
    	{
	        $posts = Post::filterBy($request)
		    			->where('type', 'deals')
		    			->paginate(config('pagination.count'));
    	}

		return PostTransformer::transform($posts->getCollection());    	
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\PostTransformer;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function index()
    {    	
		$paginator = Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])->latest()->paginate(10);

		return response([
			'paginator'	=> $paginator->only(['from', 'to', 'total']),
        	'posts'	=> PostTransformer::transform($paginator->getCollection())
		]);
    }
}

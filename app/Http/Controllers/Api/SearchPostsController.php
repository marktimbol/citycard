<?php

namespace App\Http\Controllers\Api;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class SearchPostsController extends Controller
{
	public function index()
	{
		$key = request()->s;
		
        $posts = Post::search($key)->get();
        $paginator = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                ->whereIn('id', $posts->pluck('id'))
                ->paginate(config('pagination.count'));	

        return PostTransformer::transform($paginator->getCollection());
	}
}

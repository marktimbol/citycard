<?php

namespace App\Http\Controllers\Api;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class DealsController extends Controller
{
    public function index()
    {
    	$posts = Post::getDeals()->paginate(1);

		return PostTransformer::transform($posts->getCollection());    	
    }
}

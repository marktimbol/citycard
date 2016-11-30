<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class EventsController extends Controller
{
    public function index()
    {
    	$posts = Post::getEvents()->paginate(10);

		return PostTransformer::transform($posts->getCollection());  
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
    	return Post::with('outlets.merchant:id,logo', 'photos', 'sources:name')
                    ->latest()
                    ->get();
    	// return Post::with('outlets.areas.city.country', 'photos', 'sources')
        //             ->latest()
        //             ->get();
    }
}

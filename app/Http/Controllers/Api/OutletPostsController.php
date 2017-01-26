<?php

namespace App\Http\Controllers\Api;

use App\Outlet;
use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class OutletPostsController extends Controller
{
    public function index(Outlet $outlet)
    {
    	return $outlet->posts;
    }

    public function show(Outlet $outlet, Post $post)
    {
    	return PostTransformer::transform($post);
    }
}

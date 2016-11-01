<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Outlet;
use App\Post;
use Illuminate\Http\Request;

class OutletPostsController extends Controller
{
    public function index(Outlet $outlet)
    {
    	return $outlet->posts;
    }

    public function show(Outlet $outlet, Post $post)
    {
    	return $post;
    }
}

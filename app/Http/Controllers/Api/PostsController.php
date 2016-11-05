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
    	return Post::with('outlets')->latest()->get();
    }
}

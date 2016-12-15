<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
    	$posts = Post::with('merchant', 'outlets', 'photos')->latest()->paginate(20);

    	return view('public.posts.index', compact('posts'));
    }
}

<?php

namespace App\Http\Controllers\Merchants;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{
    public function show(Post $post)
    {
    	$post->load('merchant', 'outlets', 'category', 'subcategories', 'sources', 'photos');

    	return view('merchants.posts.show', compact('post'));
    }
}

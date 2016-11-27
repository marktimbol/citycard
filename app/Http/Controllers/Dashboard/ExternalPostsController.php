<?php

namespace App\Http\Controllers\Dashboard;

use App\Source;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExternalPostsController extends Controller
{
    public function index(Source $source)
    {
    	$source->load('posts.merchant');
    	$posts = $source->posts()->latest()->get();

    	return view('dashboard.sources.posts.index', compact('source', 'posts'));
    }
}

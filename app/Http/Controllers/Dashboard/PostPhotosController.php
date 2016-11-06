<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;
use Illuminate\Http\Request;

class PostPhotosController extends Controller
{
    public function store(Request $request, Post $post)
    {
    	$photo = $post->photos()->create([
    		'url'	=> $request->file
    	]);

    	return back();
    }
}

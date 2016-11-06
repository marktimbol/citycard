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
    	$merchant = $post->load('merchant');

	    $uploadPath = sprintf('merchants/%s/posts/%s', str_slug($post->merchant->name), $post->id);
	    $file = $request->file->store($uploadPath, 's3');

    	$photo = $post->photos()->create([
    		'url'	=> $file
    	]);

    	return back();
    }
}

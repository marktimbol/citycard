<?php

namespace App\Http\Controllers\Dashboard;

use App\Photo;
use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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

    	return redirect()->route('dashboard.merchants.show', $post->merchant->id);
    }

    public function destroy(Post $post, Photo $photo)
    {       
        // Storage::delete($photo->url);
        $photo->delete();

        return back();
    }
}

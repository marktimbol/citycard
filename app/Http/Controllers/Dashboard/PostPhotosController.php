<?php

namespace App\Http\Controllers\Dashboard;

use App\Post;
use App\Photo;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\CityCard\UploadedPhoto;
use App\Http\Controllers\Controller;

class PostPhotosController extends Controller
{
    protected $selectedPhoto;

    public function __construct(UploadedPhoto $selectedPhoto)
    {
        $this->selectedPhoto = $selectedPhoto;
    }

    public function store(Request $request, Post $post)
    {
    	$post->load('merchant');

        $filename = $this->selectedPhoto->upload($request->file, str_slug($post->merchant->name), $post->id);
        $thumbnail = $this->selectedPhoto->createThumbnail(
            $request->file, str_slug($post->merchant->name), $post->id
        );

        $photo = $post->photos()->create([
            'url'   => $filename,
            'thumbnail' => $thumbnail,
        ]);

        return $photo;
    }

    public function destroy(Post $post, Photo $photo)
    {       
        // Storage::delete($photo->url);
        $photo->delete();

        return back();
    }
}

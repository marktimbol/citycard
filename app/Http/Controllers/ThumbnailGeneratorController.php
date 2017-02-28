<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Http\Request;
use App\Jobs\GeneratePostThumbnailPhotos;

class ThumbnailGeneratorController extends Controller
{
    public function index()
    {
		$photos = Photo::latest()->get();

		dispatch( new GeneratePostThumbnailPhotos($photos) );

		return 'Processing...';
    }
}

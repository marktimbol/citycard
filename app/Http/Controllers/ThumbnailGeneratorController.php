<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GeneratePostThumbnailPhotos;

class ThumbnailGeneratorController extends Controller
{
    public function index()
    {
		dispatch( new GeneratePostThumbnailPhotos );

		return 'Processing...';
    }
}

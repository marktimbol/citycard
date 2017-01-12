<?php

namespace App\Http\Controllers\Api\Post;

use App\Post;
use App\ItemForReservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemsForReservationController extends Controller
{
    public function index(Post $post)
    {
    	return ItemForReservation::whereTitle($post->title)->first();
    }
}

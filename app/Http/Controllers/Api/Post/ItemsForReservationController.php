<?php

namespace App\Http\Controllers\Api\Post;

use App\Post;
use App\ItemForReservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ItemsForReservationTransformer;

class ItemsForReservationController extends Controller
{
    public function index(Post $post)
    {
    	$item = ItemForReservation::whereTitle($post->title)->first();
    	return ItemsForReservationTransformer::transform($item);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavouritePostsController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:user_api');
	}

    public function store(Request $request, Post $post)
    {
    	$user = auth()->guard('user_api')->user();
    	$user->favourites()->attach($post);
    	
    	return $user->favourites;
    }
}

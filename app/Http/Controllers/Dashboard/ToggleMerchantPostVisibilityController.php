<?php

namespace App\Http\Controllers\Dashboard;

use App\Merchant;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToggleMerchantPostVisibilityController extends Controller
{
    public function update(Request $request, Merchant $merchant, Post $post)
    {    	
    	$post->published = ! $post->published;
    	$post->save();

    	return response()->json([
    		'success'	=> true,
    		'published'	=> $post->published
    	]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Merchant $merchant)
    {
        $posts = $merchant->posts;
        return view('dashboard.posts.index', compact('merchant', 'posts'));
    }

    public function create(Merchant $merchant)
    {    	
    	$outlets = $merchant->outlets;
    	return view('dashboard.posts.create', compact('merchant', 'outlets'));
    }

    public function store(Request $request, Merchant $merchant)
    {
    	$post = $merchant->posts()->create($request->all());
    	
    	if( $request->has('outlet_ids') ) {
    		$post->outlets()->attach(request('outlet_ids'));    		
    	}
    }
}

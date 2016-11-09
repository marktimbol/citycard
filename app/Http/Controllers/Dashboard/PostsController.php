<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Merchant $merchant)
    {
        $posts = $merchant->posts;
        return view('dashboard.merchants.posts.index', compact('merchant', 'posts'));
    }

    public function show(Merchant $merchant, Post $post)
    {
        $post->load('photos');
        $photos = $post->photos;
        
        return view('dashboard.merchants.posts.show', compact('merchant', 'post', 'photos'));
    }

    public function create(Merchant $merchant)
    {    	
    	$outlets = $merchant->outlets;
    	return view('dashboard.merchants.posts.create', compact('merchant', 'outlets'));
    }

    public function store(Request $request, Merchant $merchant)
    {
    	$post = $merchant->posts()->create($request->all());
    	
    	if( $request->has('outlet_ids') ) {
    		$post->outlets()->attach(request('outlet_ids'));    		
    	}

        flash()->success('A new post has been successfully saved.');

        return redirect()->route('dashboard.merchants.posts.show', [$post->merchant->id, $post->id]);
    }

    public function edit(Merchant $merchant, Post $post)
    {
        $post->load('outlets');
        $outlets = $merchant->outlets;

        return view('dashboard.merchants.posts.edit', compact('merchant', 'outlets', 'post'));
    }

    public function update(Request $request, Merchant $merchant, Post $post)
    {
        $post->update($request->all());

        if( $request->has('outlet_ids') ) {
            $post->outlets()->sync(request('outlet_ids'));            
        }

        flash()->success('Post information has been successfully updated.');

        return back();
    }

    public function destroy(Merchant $merchant, Post $post)
    {
        $post->delete();

        return redirect()->route('dashboard.merchants.posts.index', $merchant->id);
    }
}

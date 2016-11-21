<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\Post;
use JavaScript;
use App\Source;
use App\Merchant;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MerchantPostsController extends Controller
{
    public function index(Merchant $merchant)
    {
        $posts = $merchant->posts;
        return view('dashboard.merchants.posts.index', compact('merchant', 'posts'));
    }

    public function show(Merchant $merchant, Post $post)
    {
        $post->load('outlets', 'photos');
        $photos = $post->photos;

        return view('dashboard.merchants.posts.show', compact('merchant', 'post', 'photos'));
    }

    public function create(Merchant $merchant)
    {
        $merchant->load('outlets');
        $sources = Source::orderBy('name', 'asc')->latest()->get();

        JavaScript::put([
            'merchant'  => $merchant,
            'outlets'   => $merchant->outlets,
            'sources' => $sources
        ]);

    	return view('dashboard.merchants.posts.create', compact('merchant', 'outlets'));
    }

    public function store(Request $request, Merchant $merchant)
    {
    	$post = $merchant->posts()->create($request->all());

    	if( $request->has('outlet_ids') ) {
            $outlet_ids = explode(',', $request->outlet_ids);
    		$post->outlets()->attach($outlet_ids);
    	}

        if( $post->isExternal ) {
            $post->sources()->attach($request->source_from, [
                'link'  => $request->source_link
            ]);
        }

        return $post;
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

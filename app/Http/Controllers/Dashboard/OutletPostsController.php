<?php

namespace App\Http\Controllers\Dashboard;

use App\Post;
use App\Outlet;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletPostsController extends Controller
{
	public function create(Outlet $outlet)
	{
		return view('dashboard.outlets.posts.create', compact('outlet'));
	}

    public function store(Request $request, Outlet $outlet)
    {
        $outlet->load('merchant');
        $request['merchant_id'] = $outlet->merchant->id;

        $post = Post::create($request->all());
        $outlet->posts()->attach($post);

        flash()->success('Post has been successfully saved to the Outlet.');
        return redirect()
                ->route('dashboard.merchants.outlets.show', [
                    $outlet->merchant->id, $outlet->id
                ]);
    }

    public function destroy(Outlet $outlet, Post $post)
    {
    	$outlet->posts()->detach($post);

    	flash()->success('Post has been successfully removed from the Outlet.');
    	return back();
    }
}

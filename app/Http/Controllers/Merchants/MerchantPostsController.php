<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantPostsController extends Controller
{
	public function __construct()
	{
		// $this->middleware(function($request, $next) {
		// 	$this->merchant = Auth::user()->posts;
		// 	return next($request);
		// });
	}

    public function index()
    {
    	$posts = Auth::guard('merchant')->user()->posts;
    	return view('merchants.posts.index', compact('posts'));
    }

    public function create()
    {
        $outlets = Auth::guard('merchant')->user()->outlets;
    	return view('merchants.posts.create', compact('outlets'));
    }

    public function store(Request $request)
    {
    	$post = Merchant::createPost($request->all());

        if( $request->has('outlet_ids') )
        {
            foreach( request('outlet_ids') as $outlet_id)
            {
                $outlet = Outlet::findOrFail($outlet_id);
                $outlet->posts()->attach($post);
            }
        }

        flash()->success('A new post has been successfully saved.');

        return redirect()->route('dashboard.merchants.show', $merchant->id);
    }
}

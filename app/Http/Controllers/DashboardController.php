<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
    	$totalMerchants = Merchant::count();
    	$totalOutlets = Outlet::count();
    	$totalClerks = Clerk::count();
    	$totalPosts = Post::count();
    	$totalUsers = User::count();

    	return view('dashboard.index', compact('totalMerchants', 'totalOutlets', 'totalClerks', 'totalPosts', 'totalUsers'));
    }

    public function attachExisting()
    {
    	$merchants = Merchant::all();
    	$outlets = Outlet::all();
    	$posts = Post::all();

    	$user =  auth()->guard('admin')->user();

    	foreach( $merchants as $merchant )
    	{
    		$user->merchants()->attach($merchant->id);
    	}

    	foreach( $outlets as $outlet )
    	{
    		$user->outlets()->attach($outlet->id);
    	}    	

    	foreach( $posts as $post )
    	{
    		$user->posts()->attach($post->id);
    	}    	 

    	return 'done';   	
    }
}

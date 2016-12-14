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
    	$totalUsers = User::count();

        if( isAdmin() )
        {        
            $totalMerchants = Merchant::count();
            $totalOutlets = Outlet::count();
            $totalClerks = Clerk::count();
            $posts = Post::all();
        } else {
            $admin = auth()->guard('admin')->user();
            $admin->load('merchants', 'outlets', 'clerks', 'posts');

            $totalMerchants = $admin->merchants->count();
            $totalOutlets = $admin->outlets->count();
            $totalClerks = $admin->clerks->count();
            $posts = $admin->posts;
        }

        $totalPosts = $posts->count();
        $totalNewsFeed = $posts->where('type', 'newsfeed')->count();            
        $totalDeals = $posts->where('type', 'deals')->count();
        $totalEvents = $posts->where('type', 'events')->count();            

    	return view('dashboard.index', compact('totalMerchants', 'totalOutlets', 'totalClerks', 'totalPosts', 'totalUsers', 'totalNewsFeed', 'totalDeals', 'totalEvents'));
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

    // public function attachPosts()
    // {
    //     $posts = Post::with('merchant.areas.city.country')->get();
    //     dd($posts->toArray());

    //     foreach( $posts as $post )
    //     {
    //         foreach( $post->merchant->areas as $area )
    //         {
    //             $area->posts()->attach($post);
    //         }
            
    //         $area->city->posts()->attach($post);
    //         $area->city->country->posts()->attach($post);
    //     }
    // }
}

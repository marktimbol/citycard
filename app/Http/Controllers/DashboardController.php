<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Merchant;
use App\Outlet;
use App\Clerk;
use App\Post;
use App\User;
use App\Http\Requests;

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
}

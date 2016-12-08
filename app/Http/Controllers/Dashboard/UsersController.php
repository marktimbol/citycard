<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index()
    {
    	$users = User::latest()->get();
    	return view('dashboard.users.index', compact('users'));
    }

    public function show(User $user)
    {
    	return view('dashboard.users.show', compact('user'));
    }    
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminsController extends Controller
{
    public function index()
    {
    	$admins = Admin::orderBy('name', 'asc')->get();
    	return view('dashboard.admins.index', compact('admins'));
    }

    public function show(Admin $admin)
    {
    	$admin->load('merchants', 'outlets');
    	return view('dashboard.admins.show', compact('admin'));
    }
}

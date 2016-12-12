<?php

namespace App\Http\Controllers\Dashboard;

use App\Admin;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Controllers\Controller;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles')->orderBy('name', 'asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();
    	return view('dashboard.admins.index', compact('admins', 'roles'));
    }

    public function show(Admin $admin)
    {
    	$admin->load('merchants', 'outlets', 'posts');
        // dd($admin->toArray());
        
    	return view('dashboard.admins.show', compact('admin'));
    }

    public function create()
    {
        return view('dashboard.admins.create');
    }

    public function store(CreateAdminRequest $request)
    {
        $admin = Admin::create($request->all());

        flash()->success(sprintf('%s has been successfully saved.', $admin->name));
        return back();        
    }
}

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
        $admins = Admin::with('roles')
                    ->withCount('posts')
                    ->orderBy('name', 'asc')
                    ->get();
        $roles = Role::orderBy('name', 'asc')->get();

    	return view('dashboard.admins.index', compact('admins', 'roles'));
    }

    public function show(Admin $admin)
    {
    	$admin->load('merchants.areas', 'outlets', 'posts');

        $merchants = $admin->merchants()
            ->paginate(config('pagination.count'), ['*'], 'merchants');
        $outlets = $admin->outlets()
            ->paginate(config('pagination.count'), ['*'], 'outlets');
        $posts = $admin->posts()
            ->paginate(config('pagination.count'), ['*'], 'posts');

    	return view('dashboard.admins.show', compact('admin', 'merchants', 'outlets', 'posts'));
    }

    public function create()
    {
        return view('dashboard.admins.create');
    }

    public function store(CreateAdminRequest $request)
    {
        $admin = Admin::create($request->all());

        flash()->success(
            sprintf('%s has been successfully saved.', $admin->name)
        );
        return back();
    }
}

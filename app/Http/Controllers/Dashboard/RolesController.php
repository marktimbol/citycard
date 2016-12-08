<?php

namespace App\Http\Controllers\Dashboard;

use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function index()
    {
    	$roles = Role::orderBy('name', 'asc')->get();

    	return view('dashboard.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$role = Role::create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $role->name));
    	return back();    	
    }
}

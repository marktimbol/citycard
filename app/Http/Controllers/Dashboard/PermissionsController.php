<?php

namespace App\Http\Controllers\Dashboard;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionsController extends Controller
{
    public function index()
    {
    	$permissions = Permission::orderBy('name', 'asc')->get();

    	return view('dashboard.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$permission = Permission::create($request->all());

    	flash()->success(
            sprintf('%s has been successfully saved.', $permission->name)
        );
    	return back();    	
    }    
}

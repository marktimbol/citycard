<?php

namespace App\Http\Controllers\Dashboard;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function index()
    {
    	$roles = Role::with('permissions')->orderBy('name', 'asc')->get();
        $permissions = Permission::orderBy('name', 'asc')->get();

    	return view('dashboard.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$role = Role::create($request->all());

    	flash()->success(
            sprintf('%s has been successfully saved.', $role->name)
        );
    	return back();    	
    }

    public function destroy(Role $role)
    {
        $role->delete();

        flash()->success(
            sprintf('%s Role has been successfully removed.', $role->label)
        );
        return back();         
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolePermissionsController extends Controller
{
    public function store(Request $request, Role $role)
    {    	
    	$role->permissions()->sync($request->permissions);

    	flash()->success(sprintf('Permissions has been successfully updated.'));
    	return back();    	    	
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminRolesController extends Controller
{
    public function store(Request $request, Admin $admin)
    {    	
    	$admin->roles()->sync($request->roles);

    	flash()->success(sprintf('Roles has been successfully updated.'));
    	return back();    	    	
    }
}

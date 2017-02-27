<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletMembersController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->members()->attach($request->user_id);

    	return response()->json([
    		'status'	=> 1,
    		'message'	=> 'Member was registered.'
    	]);
    }
}

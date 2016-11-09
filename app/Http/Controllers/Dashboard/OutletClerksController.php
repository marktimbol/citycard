<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use App\Clerk;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletClerksController extends Controller
{
    public function create(Outlet $outlet)
    {
    	return view('dashboard.outlets.clerks.create', compact('outlet'));
    }

    public function store(Request $request, Outlet $outlet)
    {
    	$outlet->load('merchant');
    	$request['merchant_id'] = $outlet->merchant->id;

    	$clerk = Clerk::create($request->all());
    	$outlet->clerks()->attach($clerk);
    	
    	flash()->success('A Clerk has been successfully assigned on the Outlet.');
    	return redirect()
    	        ->route('dashboard.merchants.outlets.show', [
    	            $outlet->merchant->id, $outlet->id
    	        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\External;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExternalsController extends Controller
{
    public function index()
    {
        $externals = External::orderBy('name', 'asc')->latest()->get();
        return view('dashboard.externals.index', compact('externals'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$external = External::create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $external->name));
    	return back();
    }
}

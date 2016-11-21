<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\Source;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SourcesController extends Controller
{
    public function index()
    {
        $sources = Source::orderBy('name', 'asc')->latest()->get();
        return view('dashboard.sources.index', compact('sources'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$source = Source::create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $source->name));
    	return back();
    }
}

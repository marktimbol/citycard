<?php

namespace App\Http\Controllers\Dashboard;

use App\Country;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    public function index()
    {
    	$countries = Country::with('cities')->orderBy('name', 'asc')->get();

    	return view('dashboard.countries.index', compact('countries'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$country = Country::create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $country->name));
    	return back();
    }
}

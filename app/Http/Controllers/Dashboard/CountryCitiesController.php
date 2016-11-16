<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\Country;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CountryCitiesController extends Controller
{
    public function index(Country $country)
    {
    	$country->load('cities');
    	$cities = $country->cities;

    	return view('dashboard.cities.index', compact('country', 'cities'));
    }

    public function store(Request $request, Country $country)
    {
        $this->validate($request, [
            'name'  => 'required'
        ]);
        
    	$city = $country->cities()->create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $city->name));
    	return back();
    }
}

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
    	$country->load('cities.areas');
    	$cities = $country->cities()->orderBy('name', 'asc')->get();

    	return view('dashboard.cities.index', compact('country', 'cities'));
    }

    public function store(Request $request, Country $country)
    {
        $this->validate($request, [
            'name'  => 'required|unique:cities'
        ]);

    	$city = $country->cities()->create($request->all());

    	flash()->success(
            sprintf('%s has been successfully saved.', $city->name)
        );
    	return back();
    }
}

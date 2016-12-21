<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\City;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CityAreasController extends Controller
{
    public function index(City $city)
    {
    	$city->load('areas.outlets');        
    	$areas = $city->areas;
        
    	return view('dashboard.areas.index', compact('city', 'areas'));
    }

    public function store(Request $request, City $city)
    {
        $this->validate($request, [
            'name'  => 'required|unique:areas'
        ]);

    	$area = $city->areas()->create($request->all());

    	flash()->success(
            sprintf('%s has been successfully saved.', $area->name)
        );
    	return back();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Country;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\CityTransformer;
use App\Http\Controllers\Controller;

class CountryCitiesController extends Controller
{
    public function index(Country $country)
    {
    	$country->load('cities.areas');
    	$cities = $country->cities()->orderBy('name', 'asc')->get();

    	return CityTransformer::transform($cities);
    }
}

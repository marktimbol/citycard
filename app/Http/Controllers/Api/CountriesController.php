<?php

namespace App\Http\Controllers\Api;

use App\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CountryTransformer;

class CountriesController extends Controller
{
    public function index()
    {
    	$countries = Country::with('cities.areas')->orderBy('name', 'asc')->get();
    	return CountryTransformer::transform($countries);
    }
}

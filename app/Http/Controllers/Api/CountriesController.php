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
    	// $countries = Country::with('cities.areas')->withCount('posts')->orderBy('name', 'asc')->get();
    	$countries = Country::with(['cities' => function($query) {
    		$query->withCount('posts');
    	}, 'cities.areas' => function($query) {
    		$query->withCount('posts');
    	}])->withCount('posts')->orderBy('name', 'asc')->get();

    	return CountryTransformer::transform($countries);
    }
} 

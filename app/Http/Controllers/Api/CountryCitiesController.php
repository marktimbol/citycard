<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Country;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CountryCitiesController extends Controller
{
    public function index(Country $country)
    {
    	$country->load('cities');

    	return $country->cities;
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\City;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CityAreasController extends Controller
{
    public function index(City $city)
    {
    	$city->load('areas');

    	return $city->areas()->orderBy('name', 'asc')->get();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\AreaTransformer;
use App\Http\Controllers\Controller;

class CityAreasController extends Controller
{
    public function index(City $city)
    {
    	$city->load('areas');
    	$areas = $city->areas()->orderBy('name', 'asc')->get();

    	return AreaTransformer::transform($areas);
    }
}

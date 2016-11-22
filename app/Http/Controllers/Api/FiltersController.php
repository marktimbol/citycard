<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Post;
use App\City;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FiltersController extends Controller
{
    public function index()
    {
        if( request()->has('city_id') )
        {
            $city_id = request('city_id');
            $city = City::findOrFail($city_id);
            
            return Post::byCity($city);
        }

        return Post::latest()->get();
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Post;
use App\City;
use App\Area;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FiltersController extends Controller
{
    public function index()
    {
        if( request()->has('city_id') )
        {
            $city = City::findOrFail(request('city_id'));
            return Post::byCity($city);
        }

        if( request()->has('area_id') )
        {
            $area = Area::findOrFail(request('area_id'));
            $posts = Post::byArea($area);
            return $posts;
        }

        if( request()->has('area_ids') )
        {
            $area_ids = request()->area_ids;
            return Post::byAreas($area_ids);
        }
    }
}

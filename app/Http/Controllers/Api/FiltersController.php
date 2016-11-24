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
        return Post::filterBy(request()->all());
    }
}

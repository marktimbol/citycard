<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Area;
use App\City;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;

class FiltersController extends Controller
{
    public function index()
    {
        $paginator = Post::filterBy(request())->paginate(20);

        return response([
        	'paginator'	=> $paginator->only(['from', 'to', 'total']),
        	'posts'	=> PostTransformer::transform($paginator->getCollection())
        ]);
    }
}

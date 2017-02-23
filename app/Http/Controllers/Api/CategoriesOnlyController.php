<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CategoriesOnlyTransformer;

class CategoriesOnlyController extends Controller
{
	public function index()
	{	
	    $categories = Category::withCount('outlets')->orderBy('name', 'asc')->get();

	    return CategoriesOnlyTransformer::transform($categories);
	}
}

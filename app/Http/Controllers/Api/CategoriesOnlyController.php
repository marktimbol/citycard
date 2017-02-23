<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CategoryTransformer;

class CategoriesOnlyController extends Controller
{
	public function index()
	{	
	    $categories = Category::orderBy('name', 'asc')->get();

	    return CategoryTransformer::transform($categories);
	}
}

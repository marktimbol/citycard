<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CategoryTransformer;

class CategoriesController extends Controller
{
    public function index()
    {
    	$categories = Category::with('subcategories')->orderBy('name', 'asc')->get();

    	return CategoryTransformer::transform($categories);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index()
    {
    	$categories = Category::orderBy('name', 'asc')->get();
    	$categories->only(['id', 'name']);
    	
    	return $categories->all();
    }
}

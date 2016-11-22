<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubcategoriesController extends Controller
{
    public function index(Category $category)
    {
        $category->load('subcategories');

        return $category->subcategories;
    }
}

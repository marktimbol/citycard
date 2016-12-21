<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubcategoriesController extends Controller
{
    public function index(Category $category)
    {
        $category->load('subcategories.posts');
        $subcategories = $category->subcategories;

        return view('dashboard.categories.subcategories.index', compact('category', 'subcategories'));
    }

    public function store(Request $request, Category $category)
    {
        $this->validate($request, [
            'name'  => 'required'
        ]);

        $subcategory = $category->subcategories()->create($request->all());

        flash()->success(
            sprintf('%s has been successfully saved.', $subcategory->name)
        );
    	return back();
    }
}

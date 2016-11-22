<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('dashboard.categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('dashboard.categories.show', compact('category'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
    		'name'	=> 'required'
    	]);

    	$category = Category::create($request->all());

    	flash()->success(sprintf('%s has been successfully saved.', $category->name));
    	return back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        flash()->success(sprintf('%s has been successfully removed.', $category->name));
    	return redirect()->route('dashboard.categories.index');
    }
}

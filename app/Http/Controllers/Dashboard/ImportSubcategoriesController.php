<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportSubcategoriesController extends Controller
{
    public function store(Request $request, Category $category)
    {
    	$excel = app()->make('excel');

        $excel->load($request->file, function($reader) use ($category) {
        	$subcategories = $reader->all();
        	foreach( $subcategories as $subcategory ) {
                $category->subcategories()->create([
                    'name'  => $subcategory->name
                ]);
        	}
        	return 'Done';
        })->get();
    }
}

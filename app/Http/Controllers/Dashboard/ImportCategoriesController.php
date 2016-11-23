<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportCategoriesController extends Controller
{
    public function store(Request $request)
    {
    	$excel = app()->make('excel');

        $excel->load($request->file, function($reader) {
        	$categories = $reader->all();
        	foreach( $categories as $category ) {
                Category::create([
                    'name'  => $category->name
                ]);
        	}
        	return 'Done';
        })->get();
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportAreasController extends Controller
{
    public function store(Request $request, City $city)
    {
    	$excel = app()->make('excel');

        $excel->load($request->file, function($reader) use ($city) {
        	$areas = $reader->all();
        	foreach( $areas as $area ) {
                $city->areas()->create([
                    'name'  => $area->name
                ]);
        	}
        	return 'Done';
        })->get();
    }
}

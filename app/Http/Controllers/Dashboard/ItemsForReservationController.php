<?php

namespace App\Http\Controllers\Dashboard;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ItemsForReservationController extends Controller
{
    public function store(Request $request, Outlet $outlet)
    {    	
    	$this->validateRequest($request);

        $request['options'] = $request->reservationOptions;
    	$itemForReservation = $outlet->itemsForReservation()->create($request->all());

    	return response()->json([
            'success'    => true,
        ]);
    }

    protected function validateRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
        ]);

        $validator->sometimes('reservationOptions.*', 'required', function($input) {
            return $input->has_options == true;
        });        

        $validator->validate();        
    }    
}

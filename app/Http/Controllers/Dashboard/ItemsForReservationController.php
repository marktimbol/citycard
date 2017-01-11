<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\ItemForReservation;
use App\Outlet;
use Illuminate\Http\Request;
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

    public function destroy(Outlet $outlet, ItemForReservation $item)
    {
        $item->delete();
        $outlet->load('itemsForReservation');

        return response()->json([
            'success'    => true,
            'itemsForReservation'   => $outlet->itemsForReservation()->latest()->get()
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

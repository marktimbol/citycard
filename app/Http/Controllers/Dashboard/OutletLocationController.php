<?php

namespace App\Http\Controllers\Dashboard;

use App\Area;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletLocationController extends Controller
{
    public function update(Request $request, Outlet $outlet)
    {
    	$this->validate($request, [
    		'address'	=> 'required',
    		'area'	=> 'required'
    	]);

        // Remove existing area on the outlet
        $outlet->areas()->detach();

        $area = Area::firstOrCreate([
            'city_id'   => $request->city,
            'name'  => $request->area['value']
        ]);

        // Update the new name of the Outlet
        $outlet_name = sprintf('%s - %s', $outlet->merchant->name, $area->name);
        $outlet->update([
            'name'  => $outlet_name,
            'address'   => $request->address,
            'lat'   => $request->lat,
            'lng'   => $request->lng,
        ]);

        // Attach the new area on the Outlet
        $outlet->areas()->attach($area);

    	return response()->json([
    		'success'	=> true,
            'merchant_id'   => $outlet->merchant->id,
            'outlet_id' => $outlet->id,
    	]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use JavaScript;
use App\Area;
use App\Outlet;
use App\Merchant;
use App\Country;
use App\Http\Requests;
use App\Http\Requests\CreateOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerchantOutletsController extends Controller
{
	public function __construct()
	{
		// $this->middleware('admin');
	}

    public function index(Merchant $merchant)
    {
    	$outlets = $merchant->outlets;

    	return view('dashboard.outlets.index', compact('merchant', 'outlets'));
    }

    public function show(Merchant $merchant, Outlet $outlet)
    {
        $merchant->load('clerks');
        $outlet->load('posts', 'clerks', 'photos', 'itemsForReservation', 'areas.city.country');

        $posts = $outlet->posts()->latest()->get();
        $outletClerks = $outlet->clerks;
        $merchantClerks = $merchant->clerks->diff($outletClerks);
        $itemsForReservation = $outlet->itemsForReservation()->latest()->get();

        JavaScript::put([
            'merchant_id' => $merchant->id,
            'outlet_id' => $outlet->id,
            'admin_path'    => adminPath(),
            'itemsForReservation'   => $itemsForReservation,

            'posts'   => $posts,
            
            'update_settings_route' => sprintf('/dashboard/outlets/%s/settings', $outlet->id),
            'has_reservation'   => $outlet->has_reservation,
            'has_messaging'   => $outlet->has_messaging,
            'has_menus'   => $outlet->has_menus,
            'is_open'   => $outlet->is_open,

            'countries' => Country::orderBy('name', 'asc')->get(),
        ]);

        return view('dashboard.outlets.show', compact('merchant', 'outlet', 'posts', 'outletClerks', 'merchantClerks', 'itemsForReservation'));
    }

    public function create(Merchant $merchant)
    {
        $countries = Country::orderBy('name', 'asc')->get();
        JavaScript::put([
            'merchant'  => $merchant,
            'countries' => $countries
        ]);

    	return view('dashboard.outlets.create', compact('merchant'));
    }

    public function store(CreateOutletRequest $request, Merchant $merchant)
    {
        // Find the existing Area {$request->area},
        // if the record does not exists, then create it.
        $area = Area::firstOrCreate([
            'city_id'   => $request->city,
            'name'  => $request->area
        ]);
                
        $request['name'] = sprintf('%s - %s', $merchant->name, $area->name);
        $request['currency'] = 'AED';

    	$outlet = $merchant->outlets()->create($request->all());
		$area->outlets()->attach($outlet);

		return $outlet;
    }

    public function edit(Merchant $merchant, Outlet $outlet)
    {
		$outlet->load('categories', 'subcategories', 'areas.city.country');  
		$area = $outlet->areas->first();

		$countries = Country::orderBy('name', 'asc')->get();

		JavaScript::put([
			'outlet'	=> $outlet,
            'merchant'  => $merchant,
            'countries' => $countries
        ]);

        return view('dashboard.outlets.edit', compact('merchant', 'outlet', 'area'));
    }

    public function update(UpdateOutletRequest $request, Merchant $merchant, Outlet $outlet)
    {        
        $outlet->update($request->all());
        $outlet->areas()->sync([$request->area_id]);

		return $outlet;
    }

    public function destroy(Merchant $merchant, Outlet $outlet)
    {
        $outlet->delete();

        flash()->success('An outlet has been successfully removed.');

        return redirect()->route('dashboard.merchants.outlets.index', $merchant->id);
    }
}

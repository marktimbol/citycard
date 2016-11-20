<?php

namespace App\Http\Controllers\Dashboard;

use JavaScript;
use App\Area;
use App\Outlet;
use App\Merchant;
use App\Country;
use App\Http\Requests;
use App\Http\Requests\CreateOutletRequest;
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
        $outlet->load('posts', 'clerks', 'photos', 'areas.city.country');
		// dd($outlet->toArray());

        $posts = $outlet->posts()->latest()->get();
        $clerks = $outlet->clerks;
        $merchantClerks = $merchant->clerks->diff($clerks);

        return view('dashboard.outlets.show', compact('merchant', 'outlet', 'posts', 'clerks', 'merchantClerks'));
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
    	$outlet = $merchant->outlets()->create($request->all());

		$area = Area::findOrFail($request->area);
		$area->outlets()->attach($outlet);

		return $outlet;
    }

    public function edit(Merchant $merchant, Outlet $outlet)
    {
		$outlet->load('areas.city.country');

		$countries = Country::orderBy('name', 'asc')->get();

		JavaScript::put([
			'outlet'	=> $outlet,
            'merchant'  => $merchant,
            'countries' => $countries
        ]);

        return view('dashboard.outlets.edit', compact('merchant', 'outlet'));
    }

    public function update(Request $request, Merchant $merchant, Outlet $outlet)
    {
        $outlet->update($request->all());

		$outlet->areas()->detach();
		$outlet->areas()->attach($request->area);

		return $outlet;
    }

    public function destroy(Merchant $merchant, Outlet $outlet)
    {
        $outlet->delete();

        flash()->success('An outlet has been successfully removed.');

        return redirect()->route('dashboard.merchants.outlets.index', $merchant->id);
    }
}

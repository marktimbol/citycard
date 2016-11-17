<?php

namespace App\Http\Controllers\Dashboard;

use JavaScript;
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
        $outlet->load('posts', 'clerks', 'photos');

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
		dd($request->all());
		
		$request['area_id'] = $request->area;
        $outlet = $merchant->outlets()->create($request->all());

        return $outlet;
    }

    public function edit(Merchant $merchant, Outlet $outlet)
    {
        return view('dashboard.outlets.edit', compact('merchant', 'outlet'));
    }

    public function update(Request $request, Merchant $merchant, Outlet $outlet)
    {
        $outlet->update($request->all());

        flash()->success('An outlet information has been successfully updated.');

        return back();
    }

    public function destroy(Merchant $merchant, Outlet $outlet)
    {
        $outlet->delete();

        flash()->success('An outlet has been successfully removed.');

        return redirect()->route('dashboard.merchants.outlets.index', $merchant->id);
    }
}

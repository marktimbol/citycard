<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use Illuminate\Http\Request;

class OutletsController extends Controller
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
        $promos = $outlet->promos;
        $clerks = $outlet->clerks;
        $merchantPromos = $merchant->promos;
        
        return view('dashboard.outlets.show', compact('merchant', 'outlet', 'promos', 'clerks', 'merchantPromos'));
    }

    public function create(Merchant $merchant)
    {
    	return view('dashboard.outlets.create', compact('merchant'));
    }

    public function store(Request $request, Merchant $merchant)
    {
        $merchant->outlets()->create($request->all());
        
        flash()->success('A new outlet has been successfully saved.');

        return redirect()->route('dashboard.merchants.outlets.index', $merchant->id);
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

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use App\Promo;
use Illuminate\Http\Request;

class PromosController extends Controller
{
    public function index(Merchant $merchant)
    {
    	$promos = $merchant->promos()->latest()->get();
    	return view('dashboard.promos.index', compact('merchant', 'promos'));
    }

    public function show(Merchant $merchant, Promo $promo)
    {
        $availableIn = $promo->outlets;
        $merchantOutlets = $merchant->outlets;
    	return view('dashboard.promos.show', compact('merchant', 'promo', 'availableIn', 'merchantOutlets'));
    }

    public function create(Merchant $merchant)
    {
    	$outlets = $merchant->outlets()->latest()->get();
    	return view('dashboard.promos.create', compact('merchant', 'outlets'));
    }

    public function store(Request $request, Merchant $merchant)
    {
    	$promo = $merchant->promos()->create($request->all());

        if( $request->has('outlet_ids') )
        {        
        	$outlets = $request->outlet_ids;
        	foreach( $outlets as $outlet )
        	{
    	    	$outlet = Outlet::findOrFail($outlet);
    	    	$outlet->promos()->attach($promo);
        	}
        }

        flash()->success('A new promo has been successfully saved.');

    	return back();
    }

    public function update(Request $request, Merchant $merchant, Promo $promo)
    {
    	$promo->update($request->all());

    	$outlets = $request->outlet_ids;
    	foreach( $outlets as $outlet )
    	{
	    	$outlet = Outlet::findOrFail($outlet);
	    	$outlet->promos()->attach($promo);
    	}

        flash()->success('A promo information has been successfully updated.');

    	return back();
    }

    /**
     * Delete the selected promo and 
     * delete also the associated outlets
     */
    public function destroy(Merchant $merchant, Promo $promo)
    {
    	foreach( $merchant->outlets as $outlet )
    	{
    		$outlet->promos()->detach($promo);
    	}

    	$promo->delete();

        flash()->success('A promo has been successfully removed.');

    	return redirect()->route('dashboard.merchants.promos.index', $merchant->id);
    }
}

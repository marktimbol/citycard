<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use App\Promo;
use Illuminate\Http\Request;

class OutletPromosController extends Controller
{
    public function store(Request $request, Merchant $merchant, Outlet $outlet)
    {
		$outlet->promos()->attach($request->promo_ids);

		flash()->success('A promo has been successfully added to the Outlet.');

		return redirect()->route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]);
    }

    public function destroy(Merchant $merchant, Outlet $outlet, Promo $promo)
    {
    	$outlet->promos()->detach($promo);

    	flash()->success('A promo has been successfully removed from the Outlet.');

    	return redirect()->route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]);
    }
}

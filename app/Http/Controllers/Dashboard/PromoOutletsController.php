<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use App\Outlet;
use App\Promo;
use Illuminate\Http\Request;

class PromoOutletsController extends Controller
{
    public function store(Request $request, Merchant $merchant, Promo $promo)
    {
		$promo->outlets()->attach($request->outlet_ids);

		flash()->success('An outlet has been successfully added to the Outlet.');

		return redirect()->route('dashboard.merchants.promos.show', [$merchant->id, $promo->id]);
    }

    public function destroy(Merchant $merchant, Promo $promo, Outlet $outlet)
    {
    	$promo->outlets()->detach($outlet);

    	flash()->success('An outlet has been successfully removed from the Promo.');

    	return redirect()->route('dashboard.merchants.promos.show', [$merchant->id, $promo->id]);
    }
}

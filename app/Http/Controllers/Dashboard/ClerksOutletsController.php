<?php

namespace App\Http\Controllers\Dashboard;

use App\Clerk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClerksOutletsController extends Controller
{
    public function store(Request $request, Clerk $clerk)
    {
    	$clerk->load('merchant');

        if( $request->has('outlets') ) {
            $clerk->outlets()->sync($request->outlets);
	        flash()->success('A Clerk has been successfully assigned on the Outlet.');
        }

    	return redirect()->route('dashboard.merchants.clerks.show', [
			$clerk->merchant->id, $clerk->id
		]);
    }
}

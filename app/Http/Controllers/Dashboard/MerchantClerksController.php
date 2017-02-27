<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\Clerk;
use JavaScript;
use App\Merchant;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMerchantClerksRequest;

class MerchantClerksController extends Controller
{
    public function index(Merchant $merchant)
    {
    	$clerks = $merchant->clerks;
    	return view('dashboard.clerks.index', compact('merchant', 'clerks'));
    }

    public function show(Merchant $merchant, Clerk $clerk)
    {
        $merchant->load('outlets');
        $clerk->load('merchant:id,name', 'outlets');
        $outlets = $clerk->outlets;

        // dd($clerk->toArray());

        return view('dashboard.clerks.show', compact('merchant', 'clerk', 'outlets'));
    }

    public function create(Merchant $merchant)
    {
        JavaScript::put([
            'merchant' => $merchant
        ]);

        $merchant->load('outlets');
        $merchantOutlets = $merchant->outlets;

    	return view('dashboard.clerks.create', compact('merchant', 'merchantOutlets'));
    }

    public function store(CreateMerchantClerksRequest $request, Merchant $merchant)
    {        
        $clerk = $merchant->clerks()->create($request->all());

        // Assign clerk to outlet(s)
        if( $request->has('outlets') ) {
            $clerk->outlets()->attach($request->outlets);
        }

        auth()->guard('admin')->user()->clerks()->attach($clerk);
        
        flash()->success('A new clerk has been successfully saved.');

        return redirect()->route('dashboard.merchants.clerks.show', [$merchant->id, $clerk->id]);
    }

    public function edit(Merchant $merchant, Clerk $clerk)
    {
        $clerk->load('merchant:id,name', 'outlets');
        
        return view('dashboard.clerks.edit', compact('merchant', 'clerk'));
    }

    public function update(Request $request, Merchant $merchant, Clerk $clerk)
    {
        $clerk->update($request->all());

        flash()->success('A clerk information has been successfully updated.');

        return back();
    }

    public function destroy(Merchant $merchant, Clerk $clerk)
    {
        $clerk->delete();

        flash()->success('A clerk has been successfully removed.');

        return redirect()->route('dashboard.merchants.clerks.index', $merchant->id);
    }
}

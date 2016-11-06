<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Merchant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class MerchantsController extends Controller
{
	public function __construct()
	{
		// $this->middleware('admin');
	}

    public function index()
    {
    	$merchants = Merchant::latest()->get();
    	return view('dashboard.merchants.index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
        $outlets = $merchant->outlets;
        $clerks = $merchant->clerks;
        $posts = $merchant->posts;

        return view('dashboard.merchants.show', compact('merchant', 'outlets', 'clerks', 'posts'));
    }

    public function create()
    {
    	return view('dashboard.merchants.create');
    }

    public function store(Request $request)
    {
        $merchant = Merchant::create($request->all());
        $merchant->outlets()->create([
            'name'  => $request->name,
            'email'  => $request->email,
            'password'  => $request->password,
            'phone'  => $request->phone,
            'address1'  => '',
            'address2'  => '',
            'latitude'  => '',
            'longitude'  => '',
            'type'  => '',
            'country'  => $request->country,
            'city'  => $request->city,
            'area'  => '',
        ]);
        
        flash()->success('A new merchant has been successfully saved.');

        return redirect()->route('dashboard.merchants.show', $merchant->id);
    }

    public function edit(Merchant $merchant)
    {
        return view('dashboard.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $merchant->update($request->all());

        flash()->success('A merchant information has been successfully updated.');

        return back();
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        flash()->success('A merchant has been successfully removed.');

        return redirect()->route('dashboard.merchants.index');
    }

    public function import()
    {
        Excel::load('merchants.xls', function($reader) {

            // reader methods

        });
    }
}

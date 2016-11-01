<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use App\Clerk;
use App\Merchant;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClerksController extends Controller
{
    public function index(Merchant $merchant)
    {
    	$clerks = $merchant->clerks;
    	return view('dashboard.clerks.index', compact('merchant', 'clerks'));
    }

    public function show(Merchant $merchant, Clerk $clerk)
    {
        $outlets = $clerk->outlets;
        return view('dashboard.clerks.show', compact('merchant', 'clerk', 'outlets'));
    }

    public function create(Merchant $merchant)
    {
    	return view('dashboard.clerks.create', compact('merchant'));
    }

    public function store(Request $request, Merchant $merchant)
    {
        $merchant->clerks()->create($request->all());

        flash()->success('A new clerk has been successfully saved.');

        return redirect()->route('dashboard.merchants.clerks.index', $merchant->id);
    }

    public function edit(Merchant $merchant, Clerk $clerk)
    {
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

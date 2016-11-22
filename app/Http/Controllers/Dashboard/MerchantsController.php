<?php

namespace App\Http\Controllers\Dashboard;

use JavaScript;
use App\Country;
use App\Area;
use App\Merchant;
use App\Category;
use App\Http\Requests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMerchantRequest;

class MerchantsController extends Controller
{
	public function __construct()
	{
		// $this->middleware('admin');
	}

    public function index()
    {
    	$merchants = Merchant::with('areas.city.country')->latest()->get();

    	return view('dashboard.merchants.index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
		$merchant->load('areas.city.country');
        $outlets = $merchant->outlets()->latest()->get();
        $clerks = $merchant->clerks()->latest()->get();
        $posts = $merchant->posts()->latest()->get();

        return view('dashboard.merchants.show', compact('merchant', 'outlets', 'clerks', 'posts'));
    }

    public function create()
    {
		$countries = Country::orderBy('name', 'asc')->get();
		$categories = Category::orderBy('name', 'asc')->get();

        JavaScript::put([
            'countries' => $countries,
			'categories' => $categories
        ]);

    	return view('dashboard.merchants.create');
    }

    public function store(CreateMerchantRequest $request)
    {		
        $merchant = Merchant::create($request->all());
		$area = Area::findOrFail($request->area);
		$area->merchants()->attach($merchant);

		$merchant->categories()->attach($request->category);

		$subcategories = explode(',', $request->subcategories);
		$merchant->subcategories()->attach($subcategories);

        $outlet = $merchant->outlets()->create([
            'name'  => sprintf('%s - %s', $request->name, $area->name),
            'email'  => $request->email,
            'password'  => $request->password,
            'phone'  => $request->phone,
            'address1'  => '',
            'address2'  => '',
            'latitude'  => '',
            'longitude'  => '',
        ]);

		$outlet->areas()->attach($area);

		return $merchant;
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

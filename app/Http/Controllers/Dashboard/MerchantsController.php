<?php

namespace App\Http\Controllers\Dashboard;

use JavaScript;
use App\Country;
use App\City;
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
        $merchants = Merchant::with('areas.city.country')
                    ->latest();

        if( request()->has('search') ) {         
            $search = request()->search;
            $merchants = $merchants->where('name', 'like', '%'.$search.'%');
        }

        $merchants = $merchants->get();

    	return view('dashboard.merchants.index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
		$merchant->load('areas.city.country', 'categories.subcategories');
        $outlets = $merchant->outlets()->latest()->get();
        $clerks = $merchant->clerks()->latest()->get();
        $posts = $merchant->posts()->latest()->get();
        $categories = $merchant->categories;

        return view('dashboard.merchants.show', compact('merchant', 'outlets', 'clerks', 'posts', 'categories'));
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
        
        if( strlen($request->area) == 1 ) {
            $area = Area::findOrFail($request->area);
        } else {
            $city = City::findOrFail($request->city);
            $area = $city->areas()->create([
                'name'  => $request->area,
            ]);
        }

        // Store merchant in area
		$area->merchants()->attach($merchant);
        // Store the category of a merchant
        $category = Category::findOrFail($request->category);
		$merchant->categories()->attach($category);
        // Store Sub Categories
        $categories = explode(',', $request->subcategories);
        foreach( $categories as $value ) {
            if( strlen($value) == 1 ) {
                $merchant->subcategories()->attach($value);
            } else {
                $subcategory = $category->subcategories()->create([
                    'name'  => $value
                ]);
                $merchant->subcategories()->attach($subcategory);
            }
        }

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

        auth()->guard('admin')->user()->merchants()->attach($merchant->id);
        auth()->guard('admin')->user()->outlets()->attach($outlet->id);

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

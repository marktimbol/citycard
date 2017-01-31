<?php

namespace App\Http\Controllers\Dashboard;

use App\Area;
use App\Category;
use App\City;
use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\CreateMerchantRequest;
use App\Merchant;
use App\MerchantSubcategory;
use App\OutletSubcategory;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JavaScript;
use Maatwebsite\Excel\Excel;

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

        $merchants = $merchants->paginate(config('pagination.count'));

    	return view('dashboard.merchants.index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
		$merchant->load('areas.city.country', 'categories.subcategories');
        $outlets = $merchant->outlets()->latest()->get();
        $clerks = $merchant->clerks()->latest()->get();
        $posts = $merchant->posts()->latest()->get();
        $categories = $merchant->categories;

        JavaScript::put([
            'admin_path'    => adminPath(),
            'merchant_id'  => $merchant->id,
            'posts' => $posts
        ]);
        
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
        // Create the Merchant
        $merchant = Merchant::create($request->all());

        // Find the existing Area {$request->area},
        // if the record does not exists, then create it.
        $area = Area::firstOrCreate([
            'city_id'   => $request->city,
            'name'  => $request->area
        ]);

        // Create Outlet
        $outlet = $merchant->outlets()->create([
            'name'  => sprintf('%s - %s', $request->name, $area->name), // Zara - Al Rigga
            'email'  => $request->email,
            'password'  => $request->password,
            'phone'  => $request->phone,
            'currency'  => $request->currency,
            'address'  => $request->address,
            'lat'  => $request->lat,
            'lng'  => $request->lng,
        ]);

        // Attach Merchant & Outlet on the selected area
        $area->merchants()->attach($merchant);
		$area->outlets()->attach($outlet);

        // Store the category on Merchant & Outlet
        $merchant->categories()->attach($request->category);
		$outlet->categories()->attach($request->category);

        $subcategories = collect($request->subcategories);  
        $subcategories->map(function($item) use ($request, $merchant, $outlet) {
            // Find the existing Subcategory {$item},
            // if the record does not exists, then create it.
            $subcategory = Subcategory::firstOrCreate([
                'category_id'   => $request->category,
                'name'  => $item['value']
            ]);

            // Store Merchant & Outlet Subcategory
            $merchant->subcategories()->attach($subcategory);
            $outlet->subcategories()->attach($subcategory);
        });

        // User Report
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
        $validator = Validator::make($request->all(), [
            'name'  => 'required|min:3',
            'phone' => 'required',
            'email' => 'required|email',
            'currency'  => 'required'
        ]);

        $validator->sometimes(['password'], 'required|min:6|confirmed', function($input) {
            return $input->password != '';
        });

        $validator->validate();

        $merchant->update($request->all());

        flash()->success('A merchant information has been successfully updated.');

        return redirect()->route('dashboard.merchants.show', $merchant->id);
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        flash()->success('A merchant has been successfully removed.');
        return redirect()->route('dashboard.merchants.index');
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\CreateMerchantPostRequest;
use App\Merchant;
use App\Post;
use App\Source;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JavaScript;

class MerchantPostsController extends Controller
{
    public function index(Merchant $merchant)
    {
        $posts = $merchant->posts;
        return view('dashboard.merchants.posts.index', compact('merchant', 'posts'));
    }

    public function show(Merchant $merchant, Post $post)
    {
        $post->load('outlets', 'photos', 'outlets', 'sources', 'category', 'subcategories');
        $photos = $post->photos;

        JavaScript::put([
            'merchant'  => $merchant,
        ]);

        return view('dashboard.merchants.posts.show', compact('merchant', 'post', 'photos'));
    }

    public function create(Merchant $merchant)
    {
        $merchant->load('outlets');
        $sources = Source::orderBy('name', 'asc')->latest()->get();
		$categories = Category::orderBy('name', 'asc')->get();

        JavaScript::put([
            'merchant'  => $merchant,
            'outlets'   => $merchant->outlets,
            'sources' => $sources,
            'categories' => $categories
        ]);

    	return view('dashboard.merchants.posts.create', compact('merchant', 'outlets'));
    }

    public function store(Request $request, Merchant $merchant)
    {
        $validator = Validator::make($request->all(), [
            'source'    => 'required',
            'type'  => 'required',
            'outlet_ids'    => 'required',
            'title' => 'required|unique:posts',
            'category'  => 'required',
            'subcategories' => 'required',
            'desc' => 'required',
        ]);

        $validator->sometimes(['source_from', 'source_link'], 'required', function($input) {
            return $input->isExternal == 1;
        });

        $validator->sometimes(['event_date', 'event_time'], 'required', function($input) {
            return $input->type == 'events';
        });        

        $validator->validate();

        // When the validation passes
        $merchant->load('areas.city.country');
        $country = $merchant->areas->first()->city->country;
        $city = $merchant->areas->first()->city;
        $area = $merchant->areas->first();        

        $request['category_id'] = $request->category;

    	$post = $merchant->posts()->create($request->all());

        // Store Sub Categories
        $category = Category::findOrFail($request->category);
        $subcategories = collect(explode(',', $request->subcategories));

        $subcategory_ids = $subcategories->filter(function($value, $key) {
            return strlen($value) == 1;
        });

        $subcategory_strings = $subcategories->filter(function($value, $key) {
            return strlen($value) > 1;
        });

        if( $subcategory_ids->count() > 0 ) {
            $post->subcategories()->attach($subcategory_ids->all());
        }

        if( $subcategory_strings->count() > 0 ) {
            foreach( $subcategory_strings as $subcategory ) {
                $subcategory = $category->subcategories()->create([
                    'name'  => $subcategory
                ]);
                $post->subcategories()->attach($subcategory);                  
            }
        }          

    	if( $request->has('outlet_ids') ) {
            $outlet_ids = collect(explode(',', $request->outlet_ids));
    		$post->outlets()->attach($outlet_ids->all());
    	}

        if( $post->isExternal ) {
            $post->sources()->attach($request->source_from, [
                'link'  => $request->source_link
            ]);
        }

        $country->posts()->attach($post);
        $city->posts()->attach($post);
        $area->posts()->attach($post);
        
        auth()->guard('admin')->user()->posts()->attach($post->id);

        return $post;
    }

    public function edit(Merchant $merchant, Post $post)
    {
        $post->load('outlets');
        $outlets = $merchant->outlets;

        return view('dashboard.merchants.posts.edit', compact('merchant', 'outlets', 'post'));
    }

    public function update(Request $request, Merchant $merchant, Post $post)
    {
        $post->update($request->all());

        if( $request->has('outlet_ids') ) {
            $post->outlets()->sync(request('outlet_ids'));
        }

        flash()->success('Post information has been successfully updated.');

        return back();
    }

    public function destroy(Merchant $merchant, Post $post)
    {
        $post->delete();

        return redirect()->route('dashboard.merchants.posts.index', $merchant->id);
    }
}

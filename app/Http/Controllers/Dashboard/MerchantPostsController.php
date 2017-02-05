<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\CreateMerchantPostRequest;
use App\ItemForReservation;
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
        $this->validateRequest($request);

        $merchant->load('areas.city.country');
        $country = $merchant->areas->first()->city->country;
        $city = $merchant->areas->first()->city;
        $area = $merchant->areas->first();

        $request['category_id'] = $request->category;
    	$post = $merchant->posts()->create($request->all());

        foreach( $request->subcategories as $item )
        {
            $subcategory = Subcategory::firstOrCreate([
                'category_id'   => $request->category,
                'name'  => $item['value']
            ]);

            $post->subcategories()->attach($subcategory);
        }

        // Attach the post on the selected outlets
        $outlet_ids = collect(explode(',', $request->outlet_ids));
		$post->outlets()->attach($outlet_ids->all());

        if( $this->isForReservation() )
        {
            foreach( $outlet_ids as $outlet_id )
            {
                ItemForReservation::create([
                    'outlet_id' => $outlet_id,
                    'title' => $post->title,
                    'options'   => $request->has('reservationOptions') ? $request->reservationOptions : null,
                ]);
            }
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

        // Need to process this in background
        // Upload Photo to S3
        
        // $uploadPath = sprintf('merchants/%s/posts/%s', str_slug($merchant->name), $post->id);
        // foreach( $request->file as $file )
        // {  
        //     $path = $file->store($uploadPath, 's3');
        //     $photo = $post->photos()->create([
        //         'url'   => $path
        //     ]);        
        // }

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

        if( $request->has('source_from') )
        {
            $source = Source::firstOrCreate([
                'name'  => $request->source_from
            ]);

            $post->sources()->detach();
            $post->sources()->attach($source, [
                'link'  => $request->source_link,
            ]);
        }

        if( $request->has('category') )
        {
            $category = Category::firstOrCreate([
                'name'  => $request->category
            ]);

            $post->update([
                'category_id'   => $category->id,
            ]);

            $subcategories = collect([]);
            foreach( $request->subcategories as $item )
            {
                $subcategories->push(Subcategory::firstOrCreate([
                    'category_id'   => $category->id,
                    'name'  => $item['value']
                ]));
            }

            $post->subcategories()->sync($subcategories->pluck('id'));

        }

        if( $request->has('outlets') ) {
            $outlets = collect([]);
            foreach( $request->outlets as $outlet ) {
                $outlets->push($outlet['value']);
            }
            $post->outlets()->sync($outlets);
        }

        return response()->json([
            'success'   => true,
            'post'  => $post
        ]);

        // flash()->success('Post information has been successfully updated.');
        // return redirect()->route('dashboard.merchants.posts.show', [$merchant->id, $post->id]);
    }

    public function destroy(Merchant $merchant, Post $post)
    {
        $post->delete();

        flash()->success(sprintf('%s has been successfully removed.', $post->title));
        return redirect()->route('dashboard.merchants.posts.index', $merchant->id);
    }

    protected function validateRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'source'    => 'required',
            'type'  => 'required',
            'outlet_ids'    => 'required',
            'title' => 'required|unique:posts',
            'category'  => 'required',
            'subcategories' => 'required',
            // 'desc' => 'required',
        ]);

        $validator->sometimes(['source_from', 'source_link'], 'required', function($input) {
            return $input->isExternal == 1;
        });

        $validator->sometimes(['event_date', 'event_time', 'event_location'], 'required', function($input) {
            return $input->type == 'events';
        });

        $validator->sometimes('reservationOptions.*', 'required', function($input) {
            return $input->allow_for_reservation == true;
        });        

        $validator->validate();        
    }

    private function isForReservation()
    {
        return request()->allow_for_reservation;
    }
}

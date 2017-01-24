<?php

namespace App\Http\Controllers;

use JavaScript;
use App\Outlet;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Transformers\UserOutletTransformer;

class PagesController extends Controller
{
    public function home()
    {
    	if( auth()->check() ) {
    		return redirect()->to('posts');
    	}

    	return view('public.home');
    }

    public function explore()
    {
        $currentUser = auth()->guard('user')->user();
        $currentUser->load(['outlets']);

        $outlets = Outlet::with(['merchant.subcategories', 'posts' => function($query) {
            return $query->with('photos')->latest()->take(3)->get();
        }])->latest()->paginate(config('pagination.count'));

        JavaScript::put([
            'outlets'   => $outlets,
            'user_outlets'  => UserOutletTransformer::transform($currentUser->outlets),
            'api_token' => auth()->guard('user')->user()->api_token,
            's3_bucket_url' => getS3BucketUrl()
        ]);

        // dd($outlets->toArray());

        return view('public.explore', compact('outlets'));
    }

    public function company()
    {
        return view('public.about.company');
    }
    
    public function faqs()
    {
        return view('public.about.faqs');
    }                 
}

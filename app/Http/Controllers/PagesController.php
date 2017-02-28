<?php

namespace App\Http\Controllers;

use App\Faq;
use App\User;
use App\Clerk;
use App\Outlet;
use JavaScript;
use App\Company;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Transformers\UserOutletTransformer;
use App\Transformers\Explore\ExploreOutletsTransformer;

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
        $currentUserOutlets = [];

        if( auth()->guard('user')->check() )
        {        
            $currentUser = auth()->guard('user')->user();
            $currentUser->load('outlets');

            $currentUserOutlets = UserOutletTransformer::transform($currentUser->outlets);
        }

        $outlets = Outlet::with(['merchant.subcategories', 'posts' => function($query) {
            return $query->with('photos')->latest()->take(3)->get();
        }])->latest()->paginate(config('pagination.count'));

        if( request()->wantsJson() ) {
            return response()->json([
                'hasMorePages'  => $outlets->hasMorePages(),
                'nextPageUrl'   => $outlets->nextPageUrl(),
                'outlets' => ExploreOutletsTransformer::transform($outlets->getCollection()),
            ]);
        }   
        
        JavaScript::put([
            // User's token to Follow/unfollow an Outlet
            'api_token' => auth()->guard('user')->check() ? auth()->guard('user')->user()->api_token : '',
            // S3 url
            's3_bucket_url' => getS3BucketUrl(),
            // Infinite scroll
            'hasMorePages'  => $outlets->hasMorePages(),
            'nextPageUrl'   => $outlets->nextPageUrl(),
            // Explore Outlets
            'outlets' => ExploreOutletsTransformer::transform($outlets->getCollection()),
            // User outlets
            'user_outlets'  => $currentUserOutlets
        ]);     

        return view('public.explore', compact('outlets'));
    }

    public function company()
    {
        return view('public.about.company');
    }
    
    public function faqs()
    {
        $faqs = Faq::latest()->get();
        return view('public.about.faqs', compact('faqs'));
    }   

    public function terms()
    {
        $company = Company::first();
        return view('public.about.terms', compact('company'));
    } 

    public function privacy()
    {
        $company = Company::first();
        return view('public.about.privacy', compact('company'));
    }

    public function updateClerkPassword()
    {
        $clerks = Clerk::all();

        foreach( $clerks as $clerk )
        {
            $clerk->password = bcrypt('citycard');
            $clerk->save();
        }

        return 'Done';
    }         
}

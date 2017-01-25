<?php

namespace App\Http\Controllers;

use App\Clerk;
use App\Company;
use App\Faq;
use App\Outlet;
use App\Transformers\Explore\ExploreOutletsTransformer;
use App\Transformers\UserOutletTransformer;
use App\User;
use Illuminate\Http\Request;
use JavaScript;
use Ramsey\Uuid\Uuid;

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

        if( request()->wantsJson() ) {
            return response()->json([
                'hasMorePages'  => $outlets->hasMorePages(),
                'nextPageUrl'   => $outlets->nextPageUrl(),
                'outlets' => ExploreOutletsTransformer::transform($outlets->getCollection()),
            ]);
        }   
        
        JavaScript::put([
            // User's token to Follow/unfollow an Outlet
            'api_token' => auth()->guard('user')->user()->api_token,
            // S3 url
            's3_bucket_url' => getS3BucketUrl(),
            // Infinite scroll
            'hasMorePages'  => $outlets->hasMorePages(),
            'nextPageUrl'   => $outlets->nextPageUrl(),
            // Explore Outlets
            'outlets' => ExploreOutletsTransformer::transform($outlets->getCollection()),
            // User outlets
            'user_outlets'  => UserOutletTransformer::transform($currentUser->outlets),
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

    public function uuid()
    {
        $users = User::all();
        foreach( $users as $user )
        {
            $user->uuid = Uuid::uuid1()->toString();
            $user->save();
        }

        $clerks = Clerk::all();
        foreach( $clerks as $clerk )
        {
            $clerk->uuid = Uuid::uuid1()->toString();
            $clerk->save();
        }        

        return 'Done';
    }                 
}

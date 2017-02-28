<?php

namespace App\Http\Controllers;

use App\Outlet;
use JavaScript;
use Illuminate\Http\Request;
use App\Transformers\UserOutletTransformer;
use App\Transformers\Explore\ExploreOutletsTransformer;

class ExploresController extends Controller
{
    public function index()
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
}

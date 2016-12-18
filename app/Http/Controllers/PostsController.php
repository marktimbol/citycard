<?php

namespace App\Http\Controllers;

use App\Post;
use JavaScript;
use Illuminate\Http\Request;
use App\Transformers\PostTransformer;

class PostsController extends Controller
{
    public function index()
    {
    	$posts = Post::with('merchant', 'outlets:id,name', 'photos')
                ->published()
                ->latest()
                ->paginate(15);

        if( request()->has('s') )
        {
            $key = request()->s;
            $posts = Post::search($key)->get();
            $posts = Post::with('merchant', 'outlets:id,name', 'photos')
                    ->whereIn('id', $posts->pluck('id'))
                    ->paginate(15);
        }

    	JavaScript::put([
    		's3_bucket_url' => getS3BucketUrl()
    	]);

    	if( request()->wantsJson() ) {
    		return response()->json([
    			'hasMorePages'	=> $posts->hasMorePages(),
    			'nextPageUrl'	=> $posts->nextPageUrl(),
    			'posts'	=> PostTransformer::transform($posts->getCollection())
    		]);
    	}

    	JavaScript::put([
    		'hasMorePages'	=> $posts->hasMorePages(),
    		'nextPageUrl'	=> $posts->nextPageUrl(),
    		'posts'	=> PostTransformer::transform($posts->getCollection())
    	]);

    	return view('public.posts.index', compact('posts'));
    }
}

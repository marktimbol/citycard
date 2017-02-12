<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;
use App\Transformers\OutletTransformer;
use App\Transformers\SearchPostsTransformer;

class SearchPostsController extends Controller
{
        public function index()
        {
        	$key = request()->key;
        		
                $post_results = Post::search($key)->get();
                $outlet_results = Outlet::search($key)->get();

                $all = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                        ->whereIn('id', $post_results->pluck('id'))
                        ->get();

                $newsfeeds = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                        ->whereIn('id', $post_results->pluck('id'))
                        ->whereType('newsfeed')
                        ->get();

                $deals = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                        ->whereIn('id', $post_results->pluck('id'))
                        ->whereType('deals')
                        ->get();

                $events = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                        ->whereIn('id', $post_results->pluck('id'))
                        ->whereType('events')
                        ->get();

                $outlets = Outlet::with('photos', 'merchant')
                	->whereIn('id', $outlet_results->pluck('id'))
                	->get();

                return response()->json([
                	'all'	=> SearchPostsTransformer::transform($all),
                	'posts'	=> SearchPostsTransformer::transform($newsfeeds),
                	'deals'	=> SearchPostsTransformer::transform($deals),
                	'events' => SearchPostsTransformer::transform($events),
                	'outlets' => OutletTransformer::transform($outlets),
                ]);
        }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;
use App\Transformers\PostTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index(Request $request)
    {        
        $posts = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->where('type', 'events')
                    ->where('event_date', '>=', Carbon::now());                          
        
        if( $request->has('filter') && $request->filter == '1' )
        {        
            $from = explode('-', $request->from);
            $to = explode('-', $request->to);

            $from = Carbon::create($from[0], $from[1], $from[2], 0)->toDateTimeString();
            $to = Carbon::create($to[0], $to[1], $to[2], 23, 59, 59)->toDateTimeString();
            
        	$posts = $posts->whereBetween('event_date', [$from, $to]);
        }

        $posts = $posts->orderBy('event_date', 'asc')
                    ->paginate(config('pagination.count'));

		return PostTransformer::transform($posts->getCollection());
    }
}

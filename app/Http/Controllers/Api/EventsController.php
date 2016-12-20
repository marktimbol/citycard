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
                    ->where('event_date', '>=', date('Y-m-d'));                
                    
        if( $request->has('filter') && $request->filter == '1' )
        {        
            $from = explode('-', $request->from);
            $to = explode('-', $request->to);

            $from = Carbon::create($from[0], $from[1], $from[2], 0);
            $to = Carbon::create($to[0], $to[1], $to[2], 0);

            // dd($from->toDateTimeString(), $to->toDateTimeString());

        	$posts = $posts->whereBetween('event_date', [
                $from->toDateTimeString(), 
                $to->toDateTimeString()
            ]);
        }

        $posts = $posts->orderBy('event_date', 'asc')->paginate(15);

		return PostTransformer::transform($posts->getCollection());
    }
}

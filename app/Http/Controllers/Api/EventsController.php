<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;
use App\Transformers\EventTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index(Request $request)
    {        
        $posts = Post::with(['category', 'outlets:id,name', 'merchant', 'photos', 'sources'])
                    ->where('type', 'events');
                    
        if( $request->has('filter') )
        {        
            $from = explode('-', $request->from);
            $to = explode('-', $request->to);

            $from = Carbon::createFromDate($from[0], $from[1], $from[2]);
            $to = Carbon::createFromDate($to[0], $to[1], $to[2]);

        	$posts = $posts->whereBetween('event_date', [
                $from->toDateTimeString(), 
                $to->toDateTimeString()
            ]);
        }

        $posts = $posts->paginate(15);
        
		return EventTransformer::transform($posts->getCollection());
    }
}

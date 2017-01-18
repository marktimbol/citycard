<?php

namespace App\Http\Controllers;

use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    public function index()
    {
    	$events = Post::with('photos')
	    	->upcomingEvents()
	    	->orderBy('event_date', 'asc')
	    	->get()
	    	->groupBy(function($date) {
	    		return Carbon::parse($date->event_date)->format('F Y');
	    	});

	    // dd($events->toArray());

    	return view('public.events.index', compact('events'));
    }
}

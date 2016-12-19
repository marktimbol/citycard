<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;

class FavouritesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:user_api');
	}

    public function index()
    {
    	$user = auth()->guard('user_api')->user();

    	$favourites = $user->favourites()
    					->with('category', 'outlets', 'merchant', 'photos', 'sources')
    					->latest();

        if( request()->has('type') ) {
            $favourites = $favourites->whereType(request()->type);  

            if( request()->type == 'events' )
            {
                $favourites->where('event_date', '>=', date('Y-m-d'))
                            ->orderBy('event_date', 'asc');                 
            }
        }
    	
		return PostTransformer::transform($favourites->get());    	
    }
}

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
    	$paginator =  $user->favourites()
    					->with('category', 'outlets', 'merchant', 'photos', 'sources')
    					->latest()
    					->paginate(10);
    					
		return PostTransformer::transform($paginator->getCollection());    	
    }
}

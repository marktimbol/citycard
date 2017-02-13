<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class SearchUsersController extends Controller
{
    public function index()
    {
    	$key = request()->key;

    	$users = User::where('name', 'LIKE', "%$key%")
    		->orWhere('email', 'LIKE', "%$key%")
    		->orWhere('mobile', 'LIKE', "%$key%")
    		->get();

    	if( $users->count() > 0 )
    	{		
	    	return response()->json([
	    		'status'	=> 1,
	    		'message'	=> 'Search results',
	    		'data'	=> [
	    			'users'	=> UserTransformer::transform($users)
	    		]
	    	]);
    	}

    	return response()->json([
    		'status'	=> 0,
    		'message'	=> 'No results found.',
    		'data'	=> [
    			'users'	=> []
    		]
    	]);    	
    }
}

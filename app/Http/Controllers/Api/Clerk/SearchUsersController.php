<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class SearchUsersController extends Controller
{
    /**
     * Search members-only of the Outlet
     */
    public function index()
    {
    	$key = request()->key;
        $outlet = Outlet::findOrFail(request()->outlet_id);
    	$users = $outlet->members()
            ->where('name', 'LIKE', "%$key%")
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
    		'message'	=> 'No member(s) found.',
    		'data'	=> [
    			'users'	=> []
    		]
    	]);    	
    }
}

<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class UsersController extends Controller
{
    public function show(User $user)
    {
    	return response()->json([
    		'status'	=> 1,
    		'message'	=> 'Showing user information.',
    		'data'	=> [
    			'user'	=> UserTransformer::transform($user)
    		]
    	]);
    }
}

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

    	$users = User::where('email', $key)
    		->orWhere('mobile', $key)
    		->get();

    	return UserTransformer::transform($users);
    }
}

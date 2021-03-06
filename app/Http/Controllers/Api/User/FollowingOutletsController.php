<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\FollowingOutletsTransformer;

class FollowingOutletsController extends Controller
{
    public function index()
    {
    	$user = auth()->guard('user_api')->user();
    	$user->load('outlets.merchant');

    	return FollowingOutletsTransformer::transform($user->outlets);
    }
}

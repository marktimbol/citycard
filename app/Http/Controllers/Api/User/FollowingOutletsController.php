<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class FollowingOutletsController extends Controller
{
    public function index()
    {
    	$user = auth()->guard('user_api')->user();

    	return OutletTransformer::transform($user->outlets);
    }
}

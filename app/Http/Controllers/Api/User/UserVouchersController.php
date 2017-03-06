<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserVouchersTransformer;

class UserVouchersController extends Controller
{
    public function index()
    {
    	$user = auth()->guard('user_api')->user();
    	$user->load('vouchers.reward.outlets');

    	return UserVouchersTransformer::transform($user->vouchers);

    }
}

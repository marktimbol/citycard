<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;

class ScanQRCodeController extends Controller
{
    public function show()
    {
    	$user = User::where('uuid', request()->uuid)->first();
    	return UserTransformer::transform($user);
    }
}

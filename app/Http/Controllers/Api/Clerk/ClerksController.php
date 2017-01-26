<?php

namespace App\Http\Controllers\Api\Clerk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ClerkTransformer;

class ClerksController extends Controller
{
    public function index()
    {
    	$clerk = auth()->guard('clerk_api')->user();

    	return ClerkTransformer::transform($clerk);
    }
}

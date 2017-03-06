<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPointsController extends Controller
{
    public function index()
    {
    	return response()->json([
    		'points' => auth()->guard('user_api')->user()->transactions()->latest()->first()->balance
    	]);
    }
}

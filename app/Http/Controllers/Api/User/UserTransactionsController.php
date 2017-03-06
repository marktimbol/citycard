<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserTransactionsController extends Controller
{
    public function index()
    {
    	return auth()->guard('user_api')->user()->transactions()->latest()->get();;
    }
}

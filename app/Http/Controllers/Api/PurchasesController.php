<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Post;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:user_api');
	}

    public function store(Request $request, Post $post)
    {
    	dd($request->all());
    }
}

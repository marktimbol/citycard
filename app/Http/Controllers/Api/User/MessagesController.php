<?php

namespace App\Http\Controllers\Api\User;

use App\Clerk;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store(Request $request, Clerk $clerk)
    {
		return $request->user()
				->send($request->body)
				->to($clerk);
    }
}

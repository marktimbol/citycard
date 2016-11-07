<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store(Request $request, User $user)
    {
		return $request->user()
				->send($request->body)
				->to($user);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfirmEmailController extends Controller
{
	public function confirm($token)
	{
		$user = User::whereToken($token)->firstOrFail();
		$user->email_verified = true;
		$user->token = '';
		$user->save();

		flash()->success('Your email is now verified.');
		return redirect('/');
	}
}

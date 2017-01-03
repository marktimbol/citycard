<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Socialite;
use App\Http\Controllers\Controller;

class SocialiteAuthController extends Controller
{
    public function redirect($provider)
    {
    	return Socialite::driver($provider)->redirect();
    }

    public function handle($provider)
    {
    	try {
	    	$user = Socialite::driver($provider)->user();
    	} catch (Exception $e) {
    		return redirect()->to('auth/{$provider}');
    	}

    	$authUser = $this->findOrCreateUser($user);

    	// Login and remember the user
    	auth()->login($authUser, true);

    	flash()->success(sprintf('Welcome %s!', $user->name));
    	return redirect()->route('posts.index');
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $user
     * @return User
     */
    private function findOrCreateUser($user)
    {
    	if( $authUser = User::whereEmail($user->email)->first() ) {
    		return $authUser;
    	}

    	return User::create([
    		'name'	=> $user->name,
    		'email'	=> $user->email,
    		'password'	=> bcrypt($user->email),
    	]);
    }
}

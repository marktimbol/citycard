<?php

namespace App\Http\Controllers\Api\User;

use App\Events\User\UserInvitesAFriend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvitesController extends Controller
{
    public function store(Request $request)
    {
    	$this->validate($request, [
    		'email'	=> 'required|email'
    	]);

    	$user = auth()->guard('user_api')->user();

    	$friend = $user->invitations()->create([
    		'email'	=> $request->email
    	]);

    	$friend->load('user:id,name,email');

    	// fire an event to send an invitation
    	event( new UserInvitesAFriend($friend) );

    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'You have successfully sent the invitation to your friend.'
    	]);
    }
}

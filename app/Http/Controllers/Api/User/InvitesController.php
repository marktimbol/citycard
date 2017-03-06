<?php

namespace App\Http\Controllers\Api\User;

use App\Point;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\User\UserInvitesAFriend;

class InvitesController extends Controller
{
    public function store(Request $request)
    {        
    	$this->validate($request, [
    		'email'	=> 'required|email'
    	]);

    	$user = auth()->guard('user_api')->user();
        if( $user->alreadyInvited($request->email) )
        {
            return response()->json([
                'status'    => 0,
                'message'   => 'You already invited this friend.'
            ]);
        }

    	$friend = $user->invitations()->create([
    		'email'	=> $request->email
    	]);

        $user->givePoints(
            Point::first()->invite_friend, 
            sprintf('You received %s points for inviting your friend.', Point::first()->invite_friend)
        );

    	$friend->load('user:id,name,email');

    	// fire an event to send an invitation
    	event( new UserInvitesAFriend($friend) );

    	return response()->json([
    		'success'	=> true,
    		'message'	=> 'You have successfully sent the invitation to your friend.'
    	]);
    }
}

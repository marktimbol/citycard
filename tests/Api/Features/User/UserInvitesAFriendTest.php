<?php

use App\Events\User\UserInvitesAFriend;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserInvitesAFriendTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'John',
			'email'	=> 'john@example.com'
		]);
	}

    public function test_an_authenticated_user_invite_friends()
    {
    	$this->expectsEvents(UserInvitesAFriend::class);

        $point = factory(App\Point::class)->create([
            'invite_friend' => 50,
        ]);

    	$request = $this->post('/api/user/invites', [
    		'email'	=> 'jane@example.com'
    	]);

    	$this->seeInDatabase('user_invitations', [
    		'user_id'	=> $this->user->id,
    		'email'	=> 'jane@example.com'
    	]);

        // Initially, the user has 100 points,
        // so we added 50 points when he/she invites a friend.
        $this->seeInDatabase('transactions', [
            'user_id'   => $this->user->id,
            'description'   => sprintf('You received %s points for inviting your friend jane@example.com.', $point->invite_friend),
            'credit'    => $point->invite_friend,
            'debit' => 0,
            'balance'   => 150
        ]);

    	$this->seeJson([
    		'success'	=> true,
    	]);
    }

    public function test_an_authenticated_user_cannot_invite_same_email_address()
    {
        $this->post('/api/user/invites', [
            'email' => 'jane@example.com'
        ]);

        $request = $this->post('/api/user/invites', [
            'email' => 'jane@example.com'
        ]);

        $this->seeJson([
            'status'   => 0,
            'message'   => 'You already invited this friend.'
        ]);
    }
}

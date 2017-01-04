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

    	$request = $this->post('/api/user/invites', [
    		'email'	=> 'jane@example.com'
    	]);

    	$this->seeInDatabase('user_invitations', [
    		'user_id'	=> $this->user->id,
    		'email'	=> 'jane@example.com'
    	]);

    	$this->seeJson([
    		'success'	=> true,
    	]);

    }
}

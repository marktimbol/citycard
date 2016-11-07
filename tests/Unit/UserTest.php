<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
	use DatabaseMigrations;

    public function test_user_can_send_message_to_a_clerk()
    {
    	$user = $this->createUser([
    		'name'	=> 'John'
    	]);
    	$clerk = $this->createClerk();

    	$user->send('Hello')->to($clerk);

    	$this->seeInDatabase('threads', [
    		'user_id'	=> $user->id,
    		'clerk_id'	=> $clerk->id,
    		'body'	=> 'Hello'
    	]);
    }
}

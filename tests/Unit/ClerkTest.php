<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClerkTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_clerk_can_reply_to_a_message()
    {
    	$user = $this->createUser([
    		'name'	=> 'User'
    	]);

    	$clerk = $this->createClerk([
            'first_name'    => 'Clerk'
        ]);

    	// User
    	$user->send('Hi')->to($clerk);

    	// Clerk
    	$this->actingAs($clerk, 'clerk_api');

        $clerk->reply('Hello')->to(1);

        $this->seeInDatabase('replies', [
            'thread_id' => 1,
            'from' => $clerk->id,
            'body'  => 'Hello',
        ]);
    }
}

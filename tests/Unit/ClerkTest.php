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

    	// User sends message to clerk
    	$user->send('Hi')->to($clerk);

    	// Login as clerk
    	$this->actingAs($clerk, 'clerk_api');

        // Clerk replied to the conversation with the ID of 1.
        // TODO: The to() method should accept an instance of Thread
        $clerk->reply('Hello')->to(1);

        $this->seeInDatabase('replies', [
            'thread_id' => 1,
            'from' => $clerk->id,
            'body'  => 'Hello',
        ]);
    }
}

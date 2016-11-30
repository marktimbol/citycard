<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanVerifyTheirEmailAddressTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_user_can_verify_their_email_address()
    {
    	$user = $this->createUser([
    		'name'	=> 'John Doe',
    		'email'	=> 'john@example.com',
    		'email_verified'	=> false,
    	]);

    	$this->visit('/register/confirm/'. $user->token)
    		->seeInDatabase('users', [
    			'email'	=> $user->email,
    			'email_verified'	=> true
    		]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClerkCanChangePasswordTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark',
			'password'	=> 'oldpassword'
		]);
	}

    public function test_clerk_can_change_password_with_valid_credentials()
    {
    	$endpoint = '/api/clerk/change-password';
    	$request = $this->put($endpoint, [
    		'old_password'	=> 'oldpassword',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret'
    	]);

    	$this->seeJson([
    		'success'	=> true,
    		'message'	=> 'Your password has been successfully updated.'
    	]);
    }

    public function test_clerk_cannot_change_password_with_invalid_credentials()
    {
    	$endpoint = '/api/clerk/change-password';
    	$request = $this->put($endpoint, [
    		'old_password'	=> 'secret',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret'
    	]);

    	$this->seeJson([
    		'success'	=> false,
    		'message'	=> 'Incorrect old password.'
    	]);
    }    
}

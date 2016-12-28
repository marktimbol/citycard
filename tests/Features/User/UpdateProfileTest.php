<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateProfileTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'John',
			'email'	=> 'johns@example.com',
			'mobile'	=> '0568207189',
	        'mobile_verified' => false,
	        'email_verified' => false,			
		]);
	}

    public function test_an_authenticated_user_can_update_their_profile()
    {
    	$request = $this->put('/api/user/profile', [
    		'name'	=> 'John Doe'
    	]);

    	$this->seeInDatabase('users', [
    		'id'	=> $this->user->id,
    		'name'	=> 'John Doe'
    	])
    		->seeJson([
    			'success'	=> true,
    		]);
    }

    public function test_an_authenticated_user_can_update_their_email_address()
    {
    	$request = $this->put('/api/user/email', [
    		'email'	=> 'john@example.com'
    	]);

    	$this->seeInDatabase('users', [
    		'id'	=> $this->user->id,
    		'email'	=> 'john@example.com'
    	])
    		->seeJson([
    			'success'	=> true,
    		]);
    }

    public function test_an_authenticated_user_can_update_their_mobile_number()
    {
    	$request = $this->put('/api/user/mobile', [
    		'mobile'	=> '+971 56 820 7189'
    	]);

    	$this->seeInDatabase('users', [
    		'id'	=> $this->user->id,
    		'mobile'	=> '+971 56 820 7189'
    	])
    		->seeJson([
    			'success'	=> true,
    		]);
    }    
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChangePasswordTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->actingAsUser([
			'name'	=> 'John Doe',
			'password'	=> bcrypt('password')
		]);
	}

    public function test_a_user_can_change_his_or_her_password()
    {
    	$user = auth()->guard('user_api')->user();

    	$request = $this->json('PUT', 'api/user/account/change-password', [
    		'old_password'	=> 'password',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret'
    	])    	
    	->seeJson([
    		'message'	=> 'Your password has been successfully updated.'
    	]);
    }

    public function test_a_user_cannot_change_his_or_her_password_with_incorrect_details()
    {
    	$user = auth()->guard('user_api')->user();

    	$request = $this->json('PUT', 'api/user/account/change-password', [
    		'old_password'	=> 'password123',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret'
    	])    	
    	->seeJson([
    		'error'	=> 'Incorrect old password.'
    	]);
    }    
}

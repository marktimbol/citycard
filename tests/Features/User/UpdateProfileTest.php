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
			'name'	=> 'John Doe',
			'email'	=> 'john@doe.com',
			'mobile'	=> '0563759865'
		]);
	}

    public function test_a_user_can_update_his_or_her_profile()
    {
    	$user = auth()->guard('user_api')->user();

    	$this->json('PUT', '/api/user/profile', [
    		'name'	=> 'Mark Timbol',
    		'email'	=> 'mark.timbol@hotmail.com',
    		'mobile'	=> '+971 56 820 7189'
    	]);

    	$this->seeJson([
            'success'   => true,
    		'message'	=> 'Your profile has been successfully updated.',
    	])
    	->seeInDatabase('users', [
    		'id'	=> $user->id,
    		'name'	=> 'Mark Timbol',
    		'email'	=> 'mark.timbol@hotmail.com',
    		'mobile'	=> '+971 56 820 7189'
    	]);

    }
}

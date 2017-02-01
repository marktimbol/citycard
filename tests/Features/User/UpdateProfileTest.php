<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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
            // 'dob'   => $birth_date,
            // 'gender'    => 'male',
            // 'marital_status'    => 'single',
            // 'profession'    => 'IT',
	        'mobile_verified' => false,
	        'email_verified' => false,			
		]);
	}

    public function test_an_authenticated_user_can_update_their_profile()
    {
        $birth_date = Carbon::tomorrow()->toDateString();

    	$request = $this->put('/api/user/profile', [
    		'name'	=> 'John Doe',
            'dob'   => $birth_date,
            'gender'    => 'male',
            'marital_status'    => 'single',
            'profession'    => 'IT',
    	]);

    	$this->seeInDatabase('users', [
    		'id'	=> $this->user->id,
    		'name'	=> 'John Doe',
            'dob'   => $birth_date,
            'gender'    => 'male',
            'marital_status'    => 'single',
            'profession'    => 'IT'
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

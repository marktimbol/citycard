<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestUserCanRegisterTest extends TestCase
{
	use DatabaseMigrations;

    public function test_guest_user_can_register_on_the_website()
    {
    	$data = [
    		'email'	=> 'mark@timbol.com',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret',
    	];

    	$this->json('POST', '/api/register', $data)
    		->seeInDatabase('users', [
    			'email'	=> 'mark@timbol.com'
    		]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CanRegisterOnTheWebsiteTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_guest_user_can_register_on_the_website()
    {
    	$this->visit('/')
    		->type('john@example.com', 'email')
    		->type('John Doe', 'name')
    		->type('secret', 'password')
    		->type('secret', 'password_confirmation')
    		->press('Sign Up');

    	$this->seeInDatabase('users', [
    		'email'	=> 'john@example.com',
    		'name'	=> 'John Doe',
    	]);
    }
}

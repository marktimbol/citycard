<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CanRegisterOnTheWebsiteTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_guest_user_can_register_on_the_website()
    {
        $request = $this->post('/register', [
            'email' => 'john@example.com',
            'name'  => 'John Doe',
            'password'  => 'secret',
            'password_confirmation' => 'secret'
        ]);

    	$this->seeInDatabase('users', [
    		'email'	=> 'john@example.com',
    		'name'	=> 'John Doe',
    	]);
    }
}

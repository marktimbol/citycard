<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanLoginOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_user_can_login_on_the_app_using_email_and_password()
    {
    	$user = $this->createUser([
    		'name'	=> 'John Doe',
    		'email'	=> 'john@example.com',
    		'password'	=> bcrypt('secret')
    	]);

    	$credentials = [
    		'email'	=> 'john@example.com',
    		'password'	=> 'secret'
    	];

    	$this->json('POST', '/api/login', $credentials)
    		->seeJson([
                'authenticated' => true,
    			'email'	=> 'john@example.com',
                'api_token' => $user->api_token
    		]);
    }

    public function test_a_user_cannot_login_with_an_invalid_credentials()
    {
        $user = $this->createUser([
            'name'  => 'John Doe',
            'email' => 'john@example.com',
            'password'  => bcrypt('secret')
        ]);

        $credentials = [
            'email' => 'john@example.com',
            'password'  => 'secrets'
        ];

        $this->json('POST', '/api/login', $credentials)
            ->seeJson([
                'authenticated' => false,
                'error' => 'Invalid email or password.'
            ]);
    }
}

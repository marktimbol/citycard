<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestUserCanRegisterOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_guest_user_can_register_on_the_app()
    {
    	$data = [
    		'email'	=> 'mark@timbol.com',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret',
    	];

    	$this->json('POST', '/api/register', $data)
            ->seeJson([
                'email' => 'mark@timbol.com'
            ])
    		->seeInDatabase('users', [
    			'email'	=> 'mark@timbol.com'
    		]);
    }

    public function test_guest_user_cannot_register_with_invalid_input()
    {
        $data = [
            'email' => 'mark@timbol.com'
        ];

        $this->json('POST', '/api/register', $data)
            ->dontSeeInDatabase('users', [
                'email' => 'mark@timbol.com'
            ]);
    }
}

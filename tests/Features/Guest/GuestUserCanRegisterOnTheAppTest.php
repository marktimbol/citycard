<?php

use App\Events\User\UserRegistered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GuestUserCanRegisterOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_guest_user_can_register_on_the_app()
    {
        $this->expectsEvents(UserRegistered::class);

    	$data = [
            'name'  => 'Mark Timbol',
    		'email'	=> 'mark@timbol.com',
            'mobile'    => '+971 56 820 7189',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret',
    	];

    	$this->json('POST', '/api/register', $data)
            ->seeJson([
                'authenticated' => true,
                'email' => 'mark@timbol.com'
            ])
    		->seeInDatabase('users', [
                'name'  => 'Mark Timbol',
    			'email'	=> 'mark@timbol.com',
                'mobile'    => '+971 56 820 7189'
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

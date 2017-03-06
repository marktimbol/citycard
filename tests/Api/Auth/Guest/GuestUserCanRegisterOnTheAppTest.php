<?php

use App\Events\User\UserRegistered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GuestUserCanRegisterOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_guest_user_can_register_on_the_app_and_receive_a_certain_points()
    {
        $this->expectsEvents(UserRegistered::class);

        $point = factory(App\Point::class)->create([
            'registration'  => 100,
        ]);

    	$data = [
            'name'  => 'Mark Timbol',
    		'email'	=> 'mark@timbol.com',
            'mobile'    => '+971 56 820 7189',
    		'password'	=> 'secret',
    		'password_confirmation'	=> 'secret',
    	];

    	$request = $this->json('POST', '/api/register', $data)
            ->seeJson([
                'authenticated' => true,
                'email' => 'mark@timbol.com'
            ])
    		->seeInDatabase('users', [
                'name'  => 'Mark Timbol',
    			'email'	=> 'mark@timbol.com',
                'mobile'    => '+971 56 820 7189',
    		])
            ->seeInDatabase('transactions', [
                'user_id'   => 1,
                'description'   => sprintf('You received %s points upon registration.', $point->registration),
                'debit' => 0,
                'credit' => $point->registration,
                'balance'   => $point->registration,
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

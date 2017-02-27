<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClerkCanLoginOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_clerk_can_login_on_the_app_test()
    {
    	$zara = $this->createMerchant([
    		'name'	=> 'Zara',
    		'email'	=> 'zara@citycard.me',
    		'password'	=> 'secret',
            'api_token' => '123456'
    	]);

        $zaraAlRigga = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        $clerk = $this->createClerk([
            'merchant_id'   => $zara->id,
            'first_name'  => 'John',
            'email' => 'john@citycard.me',
            'password'  => 'secret',
            'api_token'	=> '123456',
        ]);

        $clerk->assignTo($zaraAlRigga);

        $this->seeInDatabase('outlet_clerks', [
        	'outlet_id'	=> 1,
        	'clerk_id'	=> 1,
        ]);

        $endpoint = 'http://merchant.citycard.dev/api/login';
    	$request = $this->json('POST', $endpoint, [
    		'email'	=> 'john@citycard.me',
    		'password'	=> 'secret',
    	]);

        $this->seeInDatabase('clerks', [
            'id'    => $clerk->id,
            'is_online' => true,
        ]);
        
    	$this->seeJson([
    		'status'	=> 1,
            'message'   => 'You have successfully login.',
    	]);
    }
}

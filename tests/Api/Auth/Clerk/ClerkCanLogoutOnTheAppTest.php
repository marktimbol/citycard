<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClerkCanLogoutOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

        $this->actingAsClerk([
        	'first_name'	=> 'John',
        	'is_online'	=> true,
        ]);
	}	

    public function test_a_clerk_can_logout_on_the_app_test()
    {
        $endpoint = 'http://merchant.citycard.dev/api/logout';
    	$request = $this->json('DELETE', $endpoint);
    	
        $this->seeInDatabase('clerks', [
            'id'    => $this->clerk->id,
            'is_online' => false,
        ]);
    }   
}

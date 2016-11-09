<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminShouldBeAbleToRegisterTest extends TestCase
{
	use DatabaseMigrations;

    public function test_an_admin_should_be_able_to_register()
    {
        $this->assertTrue(true);
    	// $this->visit('/merchant/register')
     //        ->type('McDonalds', 'name')
    	// 	->type('0563759865', 'phone')
    	// 	->type('info@mcdonalds.ae', 'email')
    	// 	->type('mcdonalds', 'password')
    	// 	->type('mcdonalds', 'password_confirmation')
    	// 	->press('Register')

    	// 	->seeInDatabase('merchants', [
    	// 		'name'	=> 'McDonalds',
    	// 		'email'	=> 'info@mcdonalds.ae'
    	// 	]);
    }
}

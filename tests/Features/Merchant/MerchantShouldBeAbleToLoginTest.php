<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MerchantShouldBeAbleToLoginTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_merchant_should_be_able_to_login_with_correct_credentials()
    {
    	$merchant = $this->createMerchant([
			'name'	=> 'Merchant Mark',
			'email'	=> 'info@merchant.ae',
			'password'	=> 'thepassword'    		
    	]);

    	$this->visit('/merchants/login')
    		->type('info@merchant.ae', 'email')
    		->type('thepassword', 'password')
    		->press('Login')

    		->seePageIs('/merchants');
    }

    public function test_a_merchant_should_not_be_able_to_login_with_incorrect_credentials()
    {
    	$merchant = $this->createMerchant([
			'name'	=> 'Merchant Mark',
			'email'	=> 'info@merchant.ae',
			'password'	=> 'thepassword'    		
    	]);

    	$this->visit('/merchants/login')
    		->type('info@merchant.ae', 'email')
    		->type('thepasswords', 'password')
    		->press('Login')

    		->seePageIs('/merchants/login');
    }
}

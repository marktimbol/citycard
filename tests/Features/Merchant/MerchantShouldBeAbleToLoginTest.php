<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MerchantShouldBeAbleToLoginTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_merchant_should_be_able_to_login_with_correct_credentials()
    {
    	$merchant = $this->createClerk([
			'first_name'	=> 'Mark',
			'email'	=> 'info@merchant.ae',
			'password'	=> 'password'
    	]);

		$endpoint = sprintf('%s/api/%s', merchantPath(), 'login');
		$request = $this->post($endpoint, [
			'email'	=> 'info@merchant.ae',
			'password'	=> 'password'
		]);

		$this->seeJson([
			'authenticated' => true,
		]);
    }

    public function test_a_merchant_should_not_be_able_to_login_with_incorrect_credentials()
    {
    	$merchant = $this->createClerk([
			'first_name'	=> 'Mark',
			'email'	=> 'info@merchant.ae',
			'password'	=> 'thepassword'
    	]);

		$endpoint = sprintf('%s/api/%s', merchantPath(), 'login');
		$request = $this->post($endpoint, [
			'email'	=> 'info@merchant.ae',
			'password'	=> 'passwords'
		]);

		$this->seeJson([
			'authenticated' => false,
		]);
    }
}

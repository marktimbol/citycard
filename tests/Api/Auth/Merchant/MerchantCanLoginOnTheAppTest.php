<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MerchantCanLoginOnTheAppTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_merchant_can_login_on_the_app_test()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'The Merchant',
    		'email'	=> 'merchant@citycard.me',
    		'password'	=> 'secret',
            'api_token' => '123456'
    	]);

        $endpoint = 'http://merchant.citycard.dev/api/login';
    	$request = $this->json('POST', $endpoint, [
    		'email'	=> 'merchant@citycard.me',
    		'password'	=> 'secret'
    	]);

    	$this->seeJson([
    		'authenticated'	=> true,
            'message'   => 'success',
            'api_token' => '123456'
    	]);
    }
}

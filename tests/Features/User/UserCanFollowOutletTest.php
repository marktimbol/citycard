<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanFollowOutletTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'John Doe'
		]);
	}

    public function test_an_authenticated_user_can_join_or_follow_an_outlet()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'Al Shaya'
    	]);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Zara - Al Rigga'
    	]);

    	$this->createOutlet([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Zara - Burjuman'
    	]);    	

    	$endpoint = sprintf('/api/outlets/%s/follows', $outlet->id);
    	$request = $this->post($endpoint);

    	$this->seeInDatabase('merchant_followers', [
    		'merchant_id'	=> $merchant->id,
    		'user_id'	=> $this->user->id,
    	])
    		->seeInDatabase('outlet_followers', [
    			'outlet_id'	=> $outlet->id,
    			'user_id'	=> $this->user->id,
    		])
    		->seeInDatabase('outlet_followers', [
    			'outlet_id'	=> 2,
    			'user_id'	=> $this->user->id,
    		]);    		

    }

    public function test_an_authenticated_user_can_know_if_he_or_she_followed_the_current_outlet()
    {
        $merchant = $this->createMerchant([
            'name'  => 'Al Shaya'
        ]);      

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        $outlet2 = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Rigga'
        ]);        

        $endpoint = sprintf('/api/outlets/%s/follows', $outlet->id);
        $this->post($endpoint);

        $this->json('GET', '/api/outlets/1')
            ->seeJson([
                'is_following'  => true,
            ]);

        $this->json('GET', '/api/outlets/2')
            ->seeJson([
                'is_following'  => true,
            ]);  

        $pullAndBear = $this->createMerchant([
            'name'  => 'Pull and Bear'
        ]);  

        $pullAndBearOutlet = $this->createOutlet([
            'merchant_id'   => $pullAndBear->id,
            'name'  => 'pullAndBearOutlet - Al Rigga'
        ]); 

        $this->json('GET', '/api/outlets/'. $pullAndBearOutlet->id)
            ->seeJson([
                'is_following'  => false,
            ]);                                     
    }
}

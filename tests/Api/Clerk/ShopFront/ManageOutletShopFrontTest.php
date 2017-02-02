<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageOutletShopFrontTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_an_authorized_clerk_can_view_outlet_shopfronts()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Starbucks'
    	]);

    	$this->clerk->assignTo($outlet);

    	$shop_front = $this->createOutletShopFront([
    		'outlet_id'	=> $outlet->id,
    		'url'	=> 'http://google.com/menu.jpg'
    	]);

    	$request = $this->get("/api/clerk/outlets/$outlet->id/shop_fronts");
        
    	$this->seeJson([
    		'url'	=> 'http://google.com/menu.jpg'
    	]);
    }

    public function test_an_authorized_clerk_can_delete_an_outlet_shop_front()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Starbucks'
        ]);

        $this->clerk->assignTo($outlet);

    	$shop_front = $this->createOutletShopFront([
    		'outlet_id'	=> $outlet->id,
    		'url'	=> 'http://google.com/menu.jpg'
    	]);

        $request = $this->delete("/api/clerk/outlets/$outlet->id/shop_fronts/$shop_front->id");
        
        $this->dontSeeInDatabase('shop_fronts',[
            'outlet_id' => $outlet->id,
            'url'   => 'http://google.com/menu.jpg'
        ]);
    }   
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageOutletMenusTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_an_authorized_clerk_can_view_outlet_menus()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Starbucks'
    	]);

    	$this->clerk->assignTo($outlet);

    	$menu = $this->createOutletMenu([
    		'outlet_id'	=> $outlet->id,
    		'url'	=> 'http://google.com/menu.jpg'
    	]);

    	$request = $this->get("/api/clerk/outlets/$outlet->id/menus");
        
    	$this->seeJson([
    		'url'	=> 'http://google.com/menu.jpg'
    	]);
    }

    public function test_an_authorized_clerk_can_delete_an_outlet_menu()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Starbucks'
        ]);

        $this->clerk->assignTo($outlet);

        $menu = $this->createOutletMenu([
            'outlet_id' => $outlet->id,
            'url'   => 'http://google.com/menu.jpg'
        ]);

        $request = $this->delete("/api/clerk/outlets/$outlet->id/menus/$menu->id");
        
        $this->dontSeeInDatabase('outlet_menus',[
            'outlet_id' => $outlet->id,
            'url'   => 'http://google.com/menu.jpg'
        ]);
    }    
}

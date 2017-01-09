<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CanManageItemsForReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_a_clerk_can_add_an_item_for_reservation_for_the_outlet()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

    	$endpoint = sprintf('/api/clerk/outlets/%s/items-for-reservation', $outlet->id);

    	$request = $this->post($endpoint, [
    		'title'	=> 'New Offer'
    	]);

    	$this->seeInDatabase('item_for_reservations', [
    		'outlet_id'	=> $outlet->id,
    		'title'	=> 'New Offer'
    	]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanManageMerchantPostReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin([
			'name'	=> 'John'
		]);
	}

	/**
	 * When creating a post, if the "Available for Reservation"
	 * is activated, we will allow the admin to enter different
	 * options to reserve. eg('Table for 2, 4 or 6 persons')
	 */
    public function test_an_admin_can_add_options_if_the_post_can_be_reserve()
    {
		$area = $this->createArea([
			'name'	=> 'Al Barsha'
		]);

    	$merchant = $this->createMerchant([
    		'name'	=> 'Zara'
    	]);

    	$area->merchants()->attach($merchant->id);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Zara - Al Rigga'
    	]);

    	$source = $this->createSource([
			'name'	=> 'Cobone'
		]);

    	$category = $this->createCategory([
			'name'	=> 'Fashion'
		]);		

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
		$request = $this->post($endpoint, [
            'source'    => 'external',
			'isExternal'	=> true,
			'source_from'	=> $source->id,
			'source_link'	=> 'http://google.com',

			'category'	=> '1',
			'subcategories'	=> 'Womens Clothing',

			'type'	=> 'deals',
			'outlet_ids'	=> '1',
			'title'	=> 'The Title',
			'desc'	=> 'The description',
			'file'	=> [],

			'allow_for_reservation'	=> true,
			'reservationOptions'	=> ['Table for 2']
		]);

        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => 1,
            'type'  => 'deals',
            'title' => 'The Title',
            'slug'  => 'the-title',
            'desc'=> 'The description',
			'isExternal'	=> true,
            'published'  => true
        ]);

        // $this->seeInDatabase('item_for_reservations', [
        // 	'outlet_id'	=> 1,
        // 	'title'	=> 'The Title',
        // 	'options'	=> json_decode('Table for 2')
        // ]);
    }
}

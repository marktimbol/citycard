<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageMerchantsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_view_all_the_merchants()
    {
		$area = $this->createArea([
			'name'	=> 'Deira'
		]);
    	$merchant = $this->createMerchant();
		$area->merchants()->attach($merchant);

 		$this->visit('/dashboard/merchants')
 			->see($merchant->name);
    }

    public function test_an_authenticated_user_can_view_merchant_information()
    {
		$area = $this->createArea([
			'name'	=> 'Deira'
		]);
        $merchant = $this->createMerchant();
		$area->merchants()->attach($merchant);

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
		$area->outlets()->attach($outlet);
        $clerk = $this->createClerk([
            'merchant_id'   => $merchant->id,
            'first_name'    => 'John'
        ]);

        $this->visit('/dashboard/merchants/'.$merchant->id)
            ->see($merchant->name)
            ->see($outlet->name);
    }

    public function test_an_authorized_user_can_add_a_merchant_and_store_it_as_an_outlet_as_well()
    {
		$endpoint = '/dashboard/merchants';

		$city = $this->createCity([
			'name'	=> 'Dubai'
		]);

		$area = $this->createArea([
			'city_id'	=> $city->id,
			'name'	=> 'Al Barsha'
		]);

		$category = $this->createCategory([
			'name'	=> 'Food'
		]);

		$subcategory_brunch = $this->createSubCategory([
			'category_id'	=> $category->id,
			'name'	=> 'Brunch'
		]);

		$subcategory_buffet = $this->createSubCategory([
			'category_id'	=> $category->id,
			'name'	=> 'Buffet'
		]);

		$this->post($endpoint, [
			'area'	=> $area->id,
			'category'	=> $category->id,
			'subcategories'	=> sprintf('%s,%s', $subcategory_brunch->id, $subcategory_buffet->id),
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
			'password'	=> 'secret',
			'password_confirmation'	=> 'secret',
		])
		->seeInDatabase('merchants', [
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
		])

		->seeInDatabase('merchant_categories', [
			'merchant_id'	=> 1,
			'category_id'	=> $category->id,
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> $subcategory_brunch->id,
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> $subcategory_buffet->id,
		])

		->seeInDatabase('area_merchants', [
			'area_id'	=> $area->id,
			'merchant_id'	=> 1,
		])

        ->seeInDatabase('outlets', [
            'merchant_id'   => 1,
            'name'  => 'Zara - Al Barsha',
			'phone' => '0563759865',
            'email' => 'john@example.com',
        ])

		->seeInDatabase('area_outlets', [
			'area_id'	=> $area->id,
			'outlet_id'	=> 1,
		]);

    }

    public function test_an_authorized_user_can_add_a_merchant_and_store_it_as_an_outlet_as_well_with_custom_options()
    {
    	$endpoint = '/dashboard/merchants';

    	$city = $this->createCity();

    	$food = $this->createCategory([
    		'name'	=> 'Food'
    	]);

    	$filipinoFood = $this->createSubCategory([
    		'category_id'	=> $food->id,
    		'name'	=> 'Filipino Food'
    	]);

		$response = $this->post($endpoint, [
			'city'	=> 1,
			'area'	=> 'Al Rigga',
			'category'	=> $food->id,
			'subcategories'	=> '1,Buffet',
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
			'password'	=> 'secret',
			'password_confirmation'	=> 'secret',
		]);

		$this->seeInDatabase('merchants', [
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
		])

		->seeInDatabase('merchant_categories', [
			'merchant_id'	=> 1,
			'category_id'	=> 1,
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> 1
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> 2
		])

		->seeInDatabase('area_merchants', [
			'area_id'	=> 1,
			'merchant_id'	=> 1,
		])

        ->seeInDatabase('outlets', [
            'merchant_id'   => 1,
            'name'  => 'Zara - Al Rigga',
			'phone' => '0563759865',
            'email' => 'john@example.com',
        ])

		->seeInDatabase('area_outlets', [
			'area_id'	=> 1,
			'outlet_id'	=> 1,
		]);
    }

    public function test_an_authorized_user_can_edit_a_merchant_information()
    {
    	$merchant = $this->createMerchant();

    	$this->visit('/dashboard/merchants/'.$merchant->id.'/edit')
    		->see('Update Merchant')

			->type('Updated Merchant Name', 'name')
			->type('0563759865', 'phone')
			->type('updated-email@citycard.me', 'email')

    		->press('Update')

    		->seeInDatabase('merchants', [
    			'id'	=> $merchant->id,
    			'name'	=> 'Updated Merchant Name',
    			'phone'	=> '0563759865',
    			'email'	=> 'updated-email@citycard.me'
    		]);
    }

    public function test_an_authenticated_user_can_delete_a_merchant()
    {
		$area = $this->createArea([
			'name'	=> 'Deira'
		]);
    	$merchant = $this->createMerchant();
		$area->merchants()->attach($merchant);

		$this->visit('/dashboard/merchants/'.$merchant->id)
			->press('Delete')

			->dontSeeInDatabase('merchants', [
				'id'	=> $merchant->id
			]);
    }
}

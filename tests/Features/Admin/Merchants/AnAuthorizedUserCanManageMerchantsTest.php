<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageMerchantsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_an_authorized_user_can_view_all_the_merchants()
    {
    	$this->adminSignIn();

		$area = $this->createArea([
			'name'	=> 'Deira'
		]);
    	$merchant = $this->createMerchant();
		$area->merchants()->attach($merchant);

 		$this->visit(adminPath() . '/dashboard/merchants')
 			->see($merchant->name);
    }

    public function test_an_authenticated_user_can_view_merchant_information()
    {
    	$this->adminSignIn();

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

        $this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id)
            ->see($merchant->name)
            ->see($outlet->name);
    }

    public function test_an_authorized_user_can_add_a_merchant_and_store_it_as_an_outlet_as_well()
    {
    	$this->adminSignIn();

		$endpoint = adminPath() . '/dashboard/merchants';

		$city = $this->createCity([
			'name'	=> 'Dubai'
		]);

		// $area = $this->createArea([
		// 	'city_id'	=> $city->id,
		// 	'name'	=> 'Al Barsha'
		// ]);

		$category = $this->createCategory([
			'name'	=> 'Food'
		]);

		$this->createSubCategory([
			'category_id'	=> $category->id,
			'name'	=> 'Brunch'
		]);

		$this->seeInDatabase('subcategories', [
			'id'	=> 1,
			'category_id'	=> 1,
			'name'	=> 'Brunch'
		]);

		$request = $this->post($endpoint, [
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
			'password'	=> 'secret',
			'password_confirmation'	=> 'secret',

			'city'	=> $city->id,
			'area'	=> 'Al Rigga',

			'category'	=> $category->id,
			'subcategories'	=> '1,Buffet',

			'currency'	=> 'AED'
		]);

		$this->seeInDatabase('merchants', [
			'name'	=> 'Zara',
			'phone'	=> '0563759865',
			'email'	=> 'john@example.com',
			'currency'	=> 'AED'
		]);

		$this->seeInDatabase('merchant_categories', [
			'merchant_id'	=> 1,
			'category_id'	=> $category->id,
		])

		->seeInDatabase('subcategories', [
			'category_id'	=> 1,
			'name'	=> 'Buffet'
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> 1,
		])

		->seeInDatabase('merchant_subcategories', [
			'merchant_id'	=> 1,
			'subcategory_id'	=> 2,
		]);

		$this->seeInDatabase('area_merchants', [
			'area_id'	=> 1,
			'merchant_id'	=> 1,
		])

        ->seeInDatabase('outlets', [
            'merchant_id'   => 1,
            'name'  => 'Zara - Al Rigga',
			'phone' => '0563759865',
            'email' => 'john@example.com',
			'currency'	=> 'AED'
        ])

		->seeInDatabase('area_outlets', [
			'area_id'	=> 1,
			'outlet_id'	=> 1,
		]);

		$this->seeInDatabase('admin_merchants', [
			'admin_id'	=> 1,
			'merchant_id'	=> 1,
		])

		->seeInDatabase('admin_outlets', [
			'admin_id'	=> 1,
			'outlet_id'	=> 1,
		]);

    }

    public function test_an_authorized_user_can_add_a_merchant_and_store_it_as_an_outlet_as_well_with_custom_options()
    {
    	$this->adminSignIn();

    	$endpoint = adminPath() . '/dashboard/merchants';

    	$city = $this->createCity();

    	$food = $this->createCategory([
    		'name'	=> 'Food'
    	]);

    	$filipinoFood = $this->createSubCategory([
    		'category_id'	=> $food->id,
    		'name'	=> 'Filipino Food'
    	]);

		$request = $this->post($endpoint, [
			'city'	=> $city->id,
			// 'custom_area'	=> "true",
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
		]);

		$this->seeInDatabase('merchant_categories', [
			'merchant_id'	=> 1,
			'category_id'	=> $food->id,
		])
			->seeInDatabase('subcategories', [
				'category_id'	=> 1,
				'name'	=> 'Buffet'
			])
			->seeInDatabase('merchant_subcategories', [
				'merchant_id'	=> 1,
				'subcategory_id'	=> 1
			])
			->seeInDatabase('merchant_subcategories', [
				'merchant_id'	=> 1,
				'subcategory_id'	=> 2
			]);

		$this->seeInDatabase('areas', [
			'city_id'	=> $city->id,
			'name'	=> 'Al Rigga',
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
    	$this->adminSignIn();
    	$merchant = $this->createMerchant();

    	$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/edit')
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
    	$this->adminSignIn();

		$area = $this->createArea([
			'name'	=> 'Deira'
		]);
    	$merchant = $this->createMerchant();
		$area->merchants()->attach($merchant);

		$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id)
			->press('Delete')

			->dontSeeInDatabase('merchants', [
				'id'	=> $merchant->id
			]);
    }
}

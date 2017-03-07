<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageMerchantOutletsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_view_all_the_merchants_outlets()
    {
    	$merchant = $this->createMerchant();
    	$area = $this->createArea();

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

		$area->outlets()->attach($outlet);

 		$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/outlets')
 			->see($outlet->name);
    }

    public function test_an_authenticated_user_can_view_the_outlet_information()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);
    	$clerk = $this->createClerk([
    		'merchant_id'	=> $merchant->id
    	]);
    	$outlet->clerks()->attach($clerk);

    	$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id)
    		->see($clerk->first_name);
    }

    public function test_an_authorized_user_can_add_an_outlet_to_a_merchant()
    {
    	$merchant = $this->createMerchant([
			'name'	=> 'Zara'
		]);

		$city = $this->createCity([
			'name'	=> 'Dubai',
		]);

		$area = $this->createArea([
			'city_id'	=> $city->id,
			'name'	=> 'Deira'
		]);

		$endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/outlets', $merchant->id);
		$request = $this->post($endpoint, [
			'city'	=> $city->id,
			'area'	=> 'Deira',
			'phone'	=> '0563759865',
			'address'	=> 'Building 13 - Dubai, United Arab Emirates',
			'lat'	=> '25.1421058',
			'lng'	=> '55.25091150000003',
			'email'	=> 'john@example.com',
		]);

		$this->seeInDatabase('outlets', [
			'merchant_id'	=> $merchant->id,
			'name'	=> 'Zara - Deira',
			'phone'	=> '0563759865',
			'address'	=> 'Building 13 - Dubai, United Arab Emirates',
			'lat'	=> '25.1421058',
			'lng'	=> '55.25091150000003',
			'email'	=> 'john@example.com',
		]);

		$this->seeInDatabase('area_outlets', [
			'area_id'	=> $area->id,
			'outlet_id'	=> 1,
		]);
    }

    public function test_an_authorized_user_can_toggle_the_settings_of_an_outlet()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Dubai Mall',
    		'is_open'	=> 0,
    		'has_reservation'	=> 0,
    		'has_messaging'	=> 0,
    		'has_menus'	=> 0
    	]);

    	$endpoint = sprintf(adminPath() . '/dashboard/outlets/%s/settings', $outlet->id);
    	$request = $this->put($endpoint, [
    		'is_open'	=> 1,
    		'has_reservation'	=> 1,
    		'has_messaging'	=> 1,
    		'has_menus'	=> 1,
    	]);

    	$this->seeInDatabase('outlets', [
    		'id'	=> $outlet->id,
    		'is_open'	=> 1,
    		'has_reservation'	=> 1,
    		'has_messaging'	=> 1,
    		'has_menus'	=> 1,
    	]);
    } 

    public function test_an_authorized_user_can_edit_a_merchants_outlet_information()
    {
    	$merchant = $this->createMerchant([
			'name'	=> 'Zara'
		]);
		$city = $this->createCity([
			'name'	=> 'Dubai'
		]);

		$alRigga = $this->createArea([
			'city_id'	=> $city->id,
			'name'	=> 'Al Rigga'
		]);
    	$outlet = $this->createOutlet([
			'name'	=> 'Zara - Al Rigga',
    		'merchant_id'	=> $merchant->id
    	]);
		$outlet->areas()->attach($alRigga);

		$this->seeInDatabase('area_outlets', [
			'area_id'	=> $alRigga->id,
			'outlet_id'	=> $outlet->id
		]);

		$burjuman = $this->createArea([
			'city_id'	=> $city->id,
			'name'	=> 'Burjuman'
		]);

		$endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/outlets/%s', $merchant->id, $outlet->id);

		$request = $this->put($endpoint, [
			'name'	=> 'Zara - Burjuman',
			'email'	=> 'john@example.com',
			'phone'	=> '0563759865',
			'currency'	=> 'AED',
			'city'	=> $city->id,
			'area'	=> 'Burjuman',
			'lat'	=> '1',
			'lng'	=> '2',
			'address'	=> 'Burjuman - Dubai, United Arab Emirates',
		]);

		$this->seeInDatabase('outlets', [
			'id'	=> $outlet->id,
			// 'name'	=> 'Zara - Burjuman',
			'email'	=> 'john@example.com',
			'phone'	=> '0563759865',
			'currency'	=> 'AED',
			'lat'	=> '1',
			'lng'	=> '2',
			'address'	=> 'Burjuman - Dubai, United Arab Emirates',
		])
		->seeInDatabase('area_outlets', [
			'area_id'	=> $burjuman->id,
			'outlet_id'	=> $outlet->id,
		])
		->dontSeeInDatabase('area_outlets', [
			'area_id'	=> $alRigga->id,
			'outlet_id'	=> $outlet->id
		]);
    }

    public function test_an_authenticated_user_can_delete_an_outlet()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

    	$endpoint = sprintf('%s/dashboard/merchants/%s/outlets/%s', adminPath(), $merchant->id, $outlet->id);

    	$this->delete($endpoint)
			->dontSeeInDatabase('outlets', [
				'id'	=> $outlet->id
			]);
    }
}

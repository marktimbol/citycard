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
			'area_id'	=> $area->id,
    		'merchant_id'	=> $merchant->id
    	]);

 		$this->visit('/dashboard/merchants/'.$merchant->id.'/outlets')
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

    	$this->visit('/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id)
    		->see($clerk->first_name);
    }

    public function test_an_authorized_user_can_add_an_outlet_to_a_merchant()
    {
    	$merchant = $this->createMerchant();
		$area = $this->createArea();

		$endpoint = sprintf('/dashboard/merchants/%s/outlets', $merchant->id);
		$response = $this->post($endpoint, [
			'area'	=> $area->id,
			'name'	=> 'Outlet',
			'phone'	=> '0563759865',
			'address1'	=> 'Address 1',
			'address2'	=> 'Address 2',
			'latitude'	=> '1',
			'longitude'	=> '2',
			'email'	=> 'john@example.com',
			'password'	=> 'john123',
			'password_confirmation'	=> 'john123',
		]);

		$this->seeInDatabase('outlets', [
			'merchant_id'	=> $merchant->id,
			'name'	=> 'Outlet',
			'phone'	=> '0563759865',
			'address1'	=> 'Address 1',
			'address2'	=> 'Address 2',
			'latitude'	=> '1',
			'longitude'	=> '2',
			'email'	=> 'john@example.com',
		]);

		$this->seeInDatabase('area_outlets', [
			'area_id'	=> $area->id,
			'outlet_id'	=> 1,
		]);
    }

    public function test_an_authorized_user_can_edit_a_merchants_outlet_information()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

		// Areas
		$alRigga = $this->createArea([
			'name'	=> 'Al Rigga'
		]);
		$outlet->areas()->attach($alRigga);

		$this->seeInDatabase('area_outlets', [
			'area_id'	=> $alRigga->id,
			'outlet_id'	=> $outlet->id
		]);

		$burjuman = $this->createArea([
			'name'	=> 'Burjuman'
		]);
		$endpoint = sprintf('/dashboard/merchants/%s/outlets/%s', $merchant->id, $outlet->id);

		$response = $this->put($endpoint, [
			'area'	=> $burjuman->id,
			'name'	=> 'Updated Outlet Name',
			'phone'	=> '0563759865',
			'address1'	=> 'Address 1',
			'address2'	=> 'Address 2',
			'latitude'	=> '1',
			'longitude'	=> '2',
			'email'	=> 'john@example.com'
		]);

		$this->seeInDatabase('outlets', [
			'id'	=> $outlet->id,
			'name'	=> 'Updated Outlet Name',
			'phone'	=> '0563759865',
			'address1'	=> 'Address 1',
			'address2'	=> 'Address 2',
			'latitude'	=> '1',
			'longitude'	=> '2',
			'email'	=> 'john@example.com'
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

		$this->visit('/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id)
			->press('Delete')

			->dontSeeInDatabase('outlets', [
				'id'	=> $outlet->id
			]);
    }
}

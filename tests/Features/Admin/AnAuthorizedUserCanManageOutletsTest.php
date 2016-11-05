<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageOutletsTest extends TestCase
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
    	$outlet = $this->createOutlet([
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

    	$this->visit('/dashboard/merchants/'.$merchant->id.'/outlets/create')
    		->see('Add Outlet')
			->type('Outlet Name', 'name')
			->type('0563759865', 'phone')
			->type('Address 1', 'address1')
			->type('Address 2', 'address2')
			->type('1', 'latitude')
			->type('2', 'longitude')
			->select('Branch', 'type')
			->type('United Arab Emirates', 'country')
			->type('Dubai', 'city')
			->type('Al Rigga', 'area')
			->type('email@citycard.me', 'email')
			->type('123456', 'password')
			->type('123456', 'password_confirmation')
			->press('Save')

			->seeInDatabase('outlets', [
				'merchant_id'	=> $merchant->id,
				'name'	=> 'Outlet Name',
				'phone'	=> '0563759865',
				'address1'	=> 'Address 1',
				'address2'	=> 'Address 2',
				'latitude'	=> '1',
				'longitude'	=> '2',
				'type'	=> 'Branch',
				'country'	=> 'United Arab Emirates',
				'city'	=> 'Dubai',
				'area'	=> 'Al Rigga',
				'email'	=> 'email@citycard.me',
			]);
    }

    public function test_an_authorized_user_can_edit_a_merchants_outlet_information()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

    	$this->visit('/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id.'/edit')
    		->see('Update Outlet')
			->type('Updated Outlet Name', 'name')
			->type('0563759865', 'phone')
			->type('Address 1', 'address1')
			->type('Address 2', 'address2')
			->type('1', 'latitude')
			->type('2', 'longitude')
			->select('Branch', 'type')
			->type('United Arab Emirates', 'country')
			->type('Dubai', 'city')
			->type('Al Rigga', 'area')
			->type('email@citycard.me', 'email')

    		->press('Update')

    		->seeInDatabase('outlets', [
    			'id'	=> $outlet->id,
    			'name'	=> 'Updated Outlet Name',
				'phone'	=> '0563759865',
				'address1'	=> 'Address 1',
				'address2'	=> 'Address 2',
				'latitude'	=> '1',
				'longitude'	=> '2',
				'type'	=> 'Branch',
				'country'	=> 'United Arab Emirates',
				'city'	=> 'Dubai',
				'area'	=> 'Al Rigga',
    			'email'	=> 'email@citycard.me'
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

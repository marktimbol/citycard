<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanManageOutletClerksTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_create_a_clerk_on_the_outlet()
    {
    	$outlet = $this->createOutlet();

    	$url = sprintf(adminPath() . '/dashboard/outlets/%s/clerks/create', $outlet->id);
    	$this->visit($url)
    		->see('Add new Clerk');
    }

    public function test_an_admin_can_store_a_clerk_on_the_outlet()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

    	$url = sprintf(adminPath() . '/dashboard/outlets/%s/clerks/create', $outlet->id);
    	$this->visit($url)
			->type('John', 'first_name')
			->type('Doe', 'last_name')
			->type('email@citycard.me', 'email')
			->type('0563759865', 'phone')
			->type('123456', 'password')
			->type('123456', 'password_confirmation')
			->press('Save')

			->seeInDatabase('clerks', [
				'merchant_id'	=> $merchant->id,
				'first_name'	=> 'John',
				'last_name'	=> 'Doe',
				'email'	=> 'email@citycard.me',
				'phone'	=> '0563759865',
			]);
    }

    public function test_an_admin_can_remove_a_clerk_from_the_outlet()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $clerk = $this->createClerk([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->clerks()->attach($clerk);

        $this->seeInDatabase('outlet_clerks', [
            'outlet_id' => $outlet->id,
            'clerk_id'  => $clerk->id
        ]);

        $endpoint = sprintf(adminPath() . '/dashboard/outlets/%s/clerks/%s', $outlet->id, $clerk->id);
        $this->delete($endpoint)
            ->dontSeeInDatabase('outlet_clerks', [
                'outlet_id' => $outlet->id,
                'clerk_id'   => $clerk->id
            ]);
    }
}

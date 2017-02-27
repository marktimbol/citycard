<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageMerchantClerksTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_view_all_the_merchants_clerks()
    {
        $merchant = $this->createMerchant();
        $clerk = $this->createClerk([
            'merchant_id'   => $merchant->id
        ]);

 		$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/clerks')
 			->see($clerk->name);
    }

    public function test_an_authorized_user_can_view_the_clerks_information()
    {
        $merchant = $this->createMerchant();
        $clerk = $this->createClerk([
            'merchant_id'   => $merchant->id
        ]);

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $outlet->clerks()->attach($clerk);

        $this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/clerks/'.$clerk->id)
            ->see($outlet->name);
    }

    public function test_an_authorized_user_can_add_a_clerk_to_a_merchant()
    {
    	$merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $this->createOutlet([
            'merchant_id'   => $merchant->id,
        ]);

        $request = $this->post(adminPath() . '/dashboard/merchants/'.$merchant->id.'/clerks', [
            'first_name'    => 'John',
            'last_name' => 'Doe',
            'display_name'  => 'BB8',
            'email' => 'john@example.com',
            'phone' => '0568207189',
            'password'  => 'secret',
            'password_confirmation' => 'secret',
            'outlets'   => [1, 2]
        ]);

		$this->seeInDatabase('clerks', [
			'merchant_id'	=> $merchant->id,
			'first_name'	=> 'John',
			'last_name'	=> 'Doe',
            'display_name'  => 'BB8',
			'email'	=> 'john@example.com',
			'phone'	=> '0568207189',
		])
            ->seeInDatabase('outlet_clerks', [
                'outlet_id' => 1,
                'clerk_id'  => 1,
            ])
            ->seeInDatabase('outlet_clerks', [
                'outlet_id' => 2,
                'clerk_id'  => 1,
            ]);
    }

    public function test_an_authorized_user_can_edit_a_merchants_clerk_information()
    {
        $merchant = $this->createMerchant();
    	$clerk = $this->createClerk([
            'merchant_id'   => $merchant->id
        ]);

    	$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/clerks/'.$clerk->id.'/edit')
    		->see('Update Clerk')

			->type('Updated John', 'first_name')
			->type('Updated Doe', 'last_name')
			->type('updated.email@citycard.me', 'email')
			->type('971563759865', 'phone')

    		->press('Update')

    		->seeInDatabase('clerks', [
    			'id'	=> $clerk->id,
				'first_name'	=> 'Updated John',
				'last_name'	=> 'Updated Doe',
				'email'	=> 'updated.email@citycard.me',
				'phone'	=> '971563759865',
    		]);
    }

    public function test_an_authenticated_user_can_delete_an_clerk()
    {
    	$merchant = $this->createMerchant();
    	$clerk = $this->createClerk([
            'merchant_id'   => $merchant->id
        ]);

        $endpoint = sprintf('%s/dashboard/merchants/%s/clerks/%s', adminPath(), $merchant->id, $clerk->id);
        $this->delete($endpoint);
		$this->dontSeeInDatabase('clerks', [
			'id'	=> $clerk->id
		]);
    }
}

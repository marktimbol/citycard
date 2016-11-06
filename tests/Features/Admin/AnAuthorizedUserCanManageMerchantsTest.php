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
    	$merchant = $this->createMerchant();

 		$this->visit('/dashboard/merchants')
 			->see($merchant->name);
    }

    public function test_an_authenticated_user_can_view_merchant_information()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
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
    	$this->visit('/dashboard/merchants/create')
    		->see('Add Merchant')

			->type('Merchant Name', 'name')
			->type('0563759865', 'phone')
			->type('United Arab Emirates', 'country')
			->type('Dubai', 'city')
			->type('email@citycard.me', 'email')
			->type('secret', 'password')
			->type('secret', 'password_confirmation')
			->press('Save')

			->seeInDatabase('merchants', [
				'name'	=> 'Merchant Name',
				'phone'	=> '0563759865',
				'country'	=> 'United Arab Emirates',
				'city'	=> 'Dubai',
				'email'	=> 'email@citycard.me',
			])

            ->seeInDatabase('outlets', [
                'merchant_id'   => 1,
                'name'  => 'Merchant Name',
                'email' => 'email@citycard.me',
                'phone' => '0563759865',
                'country'   => 'United Arab Emirates',
                'city'  => 'Dubai',
            ])

            ->seePageIs('/dashboard/merchants/1');
    }

    public function test_an_authorized_user_can_edit_a_merchant_information()
    {
    	$merchant = $this->createMerchant();

    	$this->visit('/dashboard/merchants/'.$merchant->id.'/edit')
    		->see('Update Merchant')

			->type('Updated Merchant Name', 'name')
			->type('0563759865', 'phone')
			->type('United Arab Emirates', 'country')
			->type('Dubai', 'city')
			->type('updated-email@citycard.me', 'email')

    		->press('Update')

    		->seeInDatabase('merchants', [
    			'id'	=> $merchant->id,
    			'name'	=> 'Updated Merchant Name',
    			'phone'	=> '0563759865',
    			'country'	=> 'United Arab Emirates',
    			'city'	=> 'Dubai',
    			'email'	=> 'updated-email@citycard.me'
    		]);
    }

    public function test_an_authenticated_user_can_delete_a_merchant()
    {
    	$merchant = $this->createMerchant();

		$this->visit('/dashboard/merchants/'.$merchant->id)
			->press('Delete')

			->dontSeeInDatabase('merchants', [
				'id'	=> $merchant->id
			]);
    }
}

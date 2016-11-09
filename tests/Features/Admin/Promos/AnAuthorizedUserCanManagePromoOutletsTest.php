<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManagePromoOutletsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_attach_an_outlet_to_a_promo()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);

    	$this->post('/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id.'/outlets', [
    		'outlet_ids'	=> [$outlet->id]
    	]);

    	$this->seeInDatabase('outlet_promos', [
			'promo_id'	=> $promo->id,
            'outlet_id' => $outlet->id,
		]);
    }

    public function test_an_authorized_user_can_detach_an_outlet_from_a_promo()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $promo->outlets()->attach($outlet);

        $this->delete('/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id.'/outlets/'.$outlet->id);

        $this->dontSeeInDatabase('outlet_promos', [
            'promo_id'  => $promo->id,
            'outlet_id' => $outlet->id,
        ]);    
    }

    public function test_an_authorized_user_can_view_all_the_associated_outlets_on_the_selected_promo()
    {
        $merchant = $this->createMerchant();
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Al Ghurair Branch'
        ]);
        $outlet->promos()->attach($promo);

        $this->visit('/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id)
            ->see('Al Ghurair Branch');
    }
}

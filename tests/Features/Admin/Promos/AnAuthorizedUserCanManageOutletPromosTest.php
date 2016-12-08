<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManageOutletPromosTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_attach_a_promo_to_an_outlet()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);

    	$this->post(adminPath() . '/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id.'/promos', [
    		'outlet_id'	=> $outlet->id,
    		'promo_ids'	=> [$promo->id]
    	])
    	
    	->seeInDatabase('outlet_promos', [
			'outlet_id'	=> $outlet->id,
			'promo_id'	=> $promo->id
		]);
    }

    public function test_an_authorized_user_can_detach_a_promo_from_an_outlet()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->promos()->attach($promo);

        $this->delete(adminPath() . '/dashboard/merchants/'.$merchant->id.'/outlets/'.$outlet->id.'/promos/'.$promo->id);

        $this->dontSeeInDatabase('outlet_promos', [
            'outlet_id' => $outlet->id,
            'promo_id'  => $promo->id,
        ]);    
    }    

    public function test_an_authorized_user_can_view_all_the_associated_outlets_on_the_selected_promo()
    {
        $merchant = $this->createMerchant();
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $outlet = $this->createOutlet([
            'name'  => 'Al Ghurair Branch'
        ]);
        $outlet->promos()->attach($promo);

        $this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id)
            ->see('Al Ghurair Branch');
    }
}

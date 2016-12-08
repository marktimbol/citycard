<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAuthorizedUserCanManagePromotionsTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_authorized_user_can_view_all_the_merchants_promotions()
    {
        $merchant = $this->createMerchant();
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);

 		$this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos')
 			->see($promo->title);
    }

    public function test_an_authorized_user_can_attach_a_promo_to_a_merchants_outlet()
    {
        $merchant = $this->createMerchant();
        $outlet1 = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $outlet2 = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $this->post(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos', [
            'title' => 'Buy 1 take 1',
            'outlet_ids' => [ $outlet1->id, $outlet2->id ],
        ])

		->seeInDatabase('promos', [
			'merchant_id'	=> $merchant->id,
			'title'	=> 'Buy 1 take 1',
		])

		->seeInDatabase('outlet_promos', [
			'outlet_id'	=> $outlet1->id,
			'promo_id'	=> 1,
		])

        ->seeInDatabase('outlet_promos', [
            'outlet_id' => $outlet2->id,
            'promo_id'  => 1,
        ]);
    }

    public function test_an_authorized_user_can_edit_a_promo_on_a_merchants_outlet()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id,
            'title' => 'Buy 1 take 1'
        ]);
        $outlet->promos()->attach($promo);

        $response = $this->put(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id, [
            'title' => 'Buy 2 take 1',
            'outlet_ids' => [ $outlet->id ],
        ]);

        $this->seeInDatabase('promos', [
            'id'    => $promo->id,
            'title' => 'Buy 2 take 1'
        ])

        ->seeInDatabase('outlet_promos', [
            'outlet_id' => $outlet->id,
            'promo_id'  => $promo->id
        ]);
    }

    public function test_an_authorized_user_can_view_the_list_of_outlets_where_the_promo_is_available()
    {
        $merchant = $this->createMerchant();
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->promos()->attach($promo);

        $this->visit(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id)
            ->see('Available in')
            ->see($outlet->name);
    }

    public function test_an_authenticated_user_can_delete_a_promo()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $promo = $this->createPromo([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->promos()->attach($promo);

        $this->delete(adminPath() . '/dashboard/merchants/'.$merchant->id.'/promos/'.$promo->id)
        
			->dontSeeInDatabase('promos', [
				'id'	=> $promo->id
			])
            ->dontSeeInDatabase('outlet_promos', [
                'promo_id'  => $promo->id
            ]);
    }
}

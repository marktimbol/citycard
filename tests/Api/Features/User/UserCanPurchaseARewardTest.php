<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanPurchaseARewardTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser();
	}

    public function test_an_authenticated_user_can_purchase_a_reward_and_make_a_voucher()
    {
    	$merchant = factory(App\Merchant::class)->create();
    	$outlet = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id
    	]);

    	$reward = factory(App\Reward::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'quantity'	=> 10,
    	]);

    	$request = $this->post('/api/user/rewards', [
    		'reward_id'	=> $reward->id,
    	]);

        $this->seeInDatabase('vouchers', [
            'user_id'   => $this->user->id,
            'reward_id' => $reward->id,
            // 'verification_code' => '1234567'
        ]);

        $this->seeInDatabase('rewards', [
             'id'    => $reward->id,
             'quantity'  => 9
         ]);
    }
}

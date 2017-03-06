<?php

use App\Voucher;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanPurchaseARewardTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
            'points'    => 10
        ]);
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
            'required_points'   => 5,
    	]);

    	$request = $this->post('/api/user/rewards/purchase', [
            'outlet_id' => $outlet->id,
            'reward_id' => $reward->id,
    	]);

        $created_voucher = Voucher::first();
        $this->seeInDatabase('vouchers', [
            'reward_id' => $reward->id,
            'verification_code' => $created_voucher->verification_code,
            'redeemed'  => false,
        ]);

        $this->seeInDatabase('rewards', [
             'id'    => $reward->id,
             'quantity'  => 9
         ]);

        $this->seeInDatabase('outlet_vouchers', [
            'outlet_id' => $outlet->id,
            'voucher_id' => $created_voucher->id,
        ]);

        $this->seeInDatabase('user_vouchers', [
            'user_id'   => $this->user->id,
            'voucher_id' => $created_voucher->id,
        ]);

        $this->seeInDatabase('users', [
            'id'    => $this->user->id,
            'points'    => 5,
        ]);        
    }

    public function test_an_authenticated_user_cannot_purchase_a_reward_if_there_is_no_enough_points()
    {        
        $merchant = factory(App\Merchant::class)->create();
        $outlet = factory(App\Outlet::class)->create([
            'merchant_id'   => $merchant->id
        ]);

        $reward = factory(App\Reward::class)->create([
            'merchant_id'   => $merchant->id,
            'quantity'  => 10,
            'required_points'   => 50,
        ]);

        $request = $this->post('/api/user/rewards/purchase', [
            'outlet_id' => $outlet->id,
            'reward_id' => $reward->id,
        ]);

        $this->seeJson([
            'status'    => 0,
            'message'   => 'You do not have enough points to purchase this reward.'
        ]);
    }    
}

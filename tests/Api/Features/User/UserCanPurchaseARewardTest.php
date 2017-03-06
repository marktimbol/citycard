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

        // Initially, the user has 100 points,
        // so we deduct 5 points when the reward was purchased
        $this->seeInDatabase('transactions', [
            'user_id'   => $this->user->id,
            'description'   => sprintf('You purchased a reward for %s points.', $reward->required_points),
            'credit'    => 0,
            'debit' => $reward->required_points,
            'balance'   => 95 
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
            'required_points'   => 200,
        ]);

        $request = $this->post('/api/user/rewards/purchase', [
            'outlet_id' => $outlet->id,
            'reward_id' => $reward->id,
        ]);

        // We should see this because the user has only 100 points.
        // This reward requires a 200 points in order to redeem it.
        $this->seeJson([
            'status'    => 0,
            'message'   => 'You do not have enough points to purchase this reward.'
        ]);
    }    
}

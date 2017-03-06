<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RedeemVoucherTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'John'
		]);
	}

    public function test_an_authorized_clerk_can_redeem_a_voucher_for_the_user()
    {
    	$customer = factory(App\User::class)->create([
    		'name'	=> 'Jane Doe',
            'points'    => 100,
    	]);

        $outlet = factory(App\Outlet::class)->create([
        	'merchant_id'	=> $this->clerk->merchant->id,
        	'name'	=> 'Caribou - Dubai Internet City'
        ]);

        $reward = factory(App\Reward::class)->create([
        	'merchant_id'	=> $this->clerk->merchant->id,
        	'title'	=> 'Free coffee',
	        'quantity'  => 10,
        ]);

        $outlet->rewards()->attach($reward);

        $voucher = factory(App\Voucher::class)->create([
            'reward_id' => $reward->id,
            'verification_code' => '1234567',
            'redeemed'  => false,
        ]);
        
        $outlet->vouchers()->attach($voucher);
        $customer->vouchers()->attach($voucher);

        $endpoint = sprintf('%s/api/clerk/outlets/%s/vouchers/redeem', merchantPath(), $outlet->id);
        $request = $this->put($endpoint, [
        	'verification_code'	=> '1234567',
        ]);

        $this->seeJson([
            'status'    => 1,
            'message'   => 'The Voucher has been successfully redeemed.',
        ]);

        $this->seeInDatabase('vouchers', [
            'id'    => $voucher->id,
            'redeemed'  => true,
        ]);
    }

    public function test_display_a_not_found_message_if_the_voucher_does_not_exists_on_the_outlet()
    {
        $customer = factory(App\User::class)->create([
            'name'  => 'Jane Doe',
            'points'    => 100,
        ]);

        $outlet = factory(App\Outlet::class)->create([
            'merchant_id'   => $this->clerk->merchant->id,
            'name'  => 'Caribou - Dubai Internet City'
        ]);

        $reward = factory(App\Reward::class)->create([
            'merchant_id'   => $this->clerk->merchant->id,
            'title' => 'Free coffee',
            'quantity'  => 10,
            'required_points'   => 90,
        ]);

        $outlet->rewards()->attach($reward);

        $voucher = factory(App\Voucher::class)->create([
            'reward_id' => $reward->id,
            'verification_code' => '1234567',
            'redeemed'  => false,
        ]);
        
        $outlet->vouchers()->attach($voucher);
        $customer->vouchers()->attach($voucher);

        $endpoint = sprintf('%s/api/clerk/outlets/%s/vouchers/redeem', merchantPath(), $outlet->id);
        $this->put($endpoint, [
            'verification_code' => '123456',
        ]);

        $this->seeJson([
            'status'    => 0,
            'message'   => 'The Voucher: 123456 does not exists.'
        ]);
    }

    public function test_an_authorized_clerk_cannot_use_the_same_voucher_again()
    {
        $customer = factory(App\User::class)->create([
            'name'  => 'Jane Doe',
            'points'    => 100,
        ]);

        $outlet = factory(App\Outlet::class)->create([
            'merchant_id'   => $this->clerk->merchant->id,
            'name'  => 'Caribou - Dubai Internet City'
        ]);

        $reward = factory(App\Reward::class)->create([
            'merchant_id'   => $this->clerk->merchant->id,
            'title' => 'Free coffee',
            'quantity'  => 10,
            'required_points'   => 90,
        ]);

        $outlet->rewards()->attach($reward);

        $voucher = factory(App\Voucher::class)->create([
            'reward_id' => $reward->id,
            'verification_code' => '1234567',
            'redeemed'  => false,
        ]);
        
        $outlet->vouchers()->attach($voucher);
        $customer->vouchers()->attach($voucher);

        $endpoint = sprintf('%s/api/clerk/outlets/%s/vouchers/redeem', merchantPath(), $outlet->id);
        $this->put($endpoint, [
            'verification_code' => '1234567',
        ]);

        $this->seeInDatabase('vouchers', [
            'id'    => $voucher->id,
            'redeemed'  => true,
        ]);

        // Use the same voucher again.
        $request = $this->put($endpoint, [
            'verification_code' => '1234567',
        ]);

        $this->seeJson([
            'status'    => 0,
            'message'   => 'The Voucher: 1234567 was already used.'
        ]);
    }
}

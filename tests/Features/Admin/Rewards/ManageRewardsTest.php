<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageRewardsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin();
	}

    public function test_an_admin_can_view_all_the_rewards()
    {    	
    	$merchant = factory(App\Merchant::class)->create([
    		'name'	=> 'Costa',
    	]);

    	$outlet = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Costa - Dubai Internet City'
    	]);

    	$reward = factory(App\Reward::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'title'	=> 'Buy this offer for 10 points'
    	]);

    	$outlet->rewards()->attach($reward);

    	$this->visit(adminPath() . '/dashboard/rewards')
    		->see('Buy this offer for 10 points');
    }

    public function test_an_admin_can_create_a_reward()
    {
    	$merchant = factory(App\Merchant::class)->create([
    		'name'	=> 'Caribou'
    	]);

    	$caribou_internetCity = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Dubai Internet City'
    	]);

    	$caribou_alRigga = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Al Rigga'
    	]);

    	$endpoint = adminPath() . '/dashboard/rewards';
    	$request = $this->post($endpoint, [
    		'merchant_id'	=> $merchant->id,
    		'outlets'	=> '1,2',
    		'title'	=> 'Buy this offer for 10 points',
    		'quantity'	=> 10,
    		'required_points'	=> 100,
    		'desc'	=> 'As the title says.',
    	]);

    	$this->seeInDatabase('rewards', [
    		'merchant_id'	=> $merchant->id,
    		'title'	=> 'Buy this offer for 10 points',
    		'quantity'	=> 10,
    		'required_points'	=> 100,
    		'desc'	=> 'As the title says.',
    	])
    		->seeInDatabase('outlet_rewards', [
    			'outlet_id'	=> $caribou_internetCity->id,
    			// 'reward_id'	=> $request->id,
    		])
    		->seeInDatabase('outlet_rewards', [
    			'outlet_id'	=> $caribou_alRigga->id,
    			// 'reward_id'	=> $request->id,
    		]);
    }
}

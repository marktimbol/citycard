<?php

use Carbon\Carbon;
use App\CityCard\ShoppingCart;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserPurchaseAnOfferTest extends TestCase
{
	use DatabaseMigrations;

	protected $cart;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'John Doe'
		]);

		$this->cart = new ShoppingCart;
	}

    public function test_an_authenticated_user_can_purchase_an_offer()
    {
    	$merchant = factory(App\Merchant::class)->create([
    		'name'	=> 'Caribou'
    	]);

    	$outlet_internetCity = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Dubai Internet City'
    	]);

    	$outlet_alRigga = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Al Rigga'
    	]);

    	$deal = factory(App\Post::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'title'	=> 'Buy 1 take 1 coffee only at Caribou',
    		'type'	=> 'deal',
    		'price'	=> 49
    	]);

    	// Make the post available to the selected outlets
    	$deal->outlets()->sync([$outlet_internetCity->id, $outlet_alRigga->id]);

    	$endpoint = sprintf('/api/user/posts/%s/purchase', $deal->id);
    	$request = $this->post($endpoint, [
    		'outlet_id'	=> $outlet_internetCity->id,
    	]);

    	$this->assertEquals(1, $this->cart->count());
    }

    public function test_an_authenticated_user_can_checkout_the_orders()
    {
    	$merchant = factory(App\Merchant::class)->create([
    		'name'	=> 'Caribou'
    	]);

    	$outlet_internetCity = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Dubai Internet City'
    	]);

    	$outlet_alRigga = factory(App\Outlet::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Caribou - Al Rigga'
    	]);

    	$deal = factory(App\Post::class)->create([
    		'merchant_id'	=> $merchant->id,
    		'title'	=> 'Buy 1 take 1 coffee only at Caribou',
    		'type'	=> 'deal',
    		'price'	=> 49
    	]);

    	// Make the post available to the selected outlets
    	$deal->outlets()->sync([$outlet_internetCity->id, $outlet_alRigga->id]);

    	$endpoint = sprintf('/api/user/posts/%s/purchase', $deal->id);

    	$this->cart->add($deal, [
    		'outlet_id'	=> $outlet_internetCity->id
    	]);

    	$request = $this->post('/api/user/checkout', [
    		'address'	=> '123 Clock Plaza, Al Rigga, Dubai, United Arab Emirates',
    		'city'	=> 'Dubai',
    		'country'	=> 'United Arab Emirates',
    		'phone'	=> '0568207189',
    		'email'	=> 'email@example.com'
    	]);

    	$this->seeInDatabase('orders', [
    		'user_id'	=> $this->user->id,
    		'outlet_id'	=> $outlet_internetCity->id,
    		'amount'	=> 49,
    		'address'	=> '123 Clock Plaza, Al Rigga, Dubai, United Arab Emirates',
    		'city'	=> 'Dubai',
    		'country'	=> 'United Arab Emirates',
    		'phone'	=> '0568207189',
    		'email'	=> 'email@example.com',
    	]);

    	$created_order = App\Order::first();
    	$this->seeInDatabase('order_details', [
    		'order_id'	=> $created_order->id,
    		'product_id'	=> $deal->id,
    		'name'	=> $deal->title,
    		'price'	=> $deal->price,
    		'quantity'	=> 1,
    	]);
    }
}
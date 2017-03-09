<?php

namespace App\Http\Controllers\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\CityCard\ShoppingCart;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
	protected $cart;

	public function __construct(ShoppingCart $cart)
	{
		$this->cart = $cart;
	}

    public function store(Request $request)
    {
    	$user = auth()->guard('user_api')->user();

    	foreach( $this->cart->content() as $item )
    	{
   	    	$order = $user->orders()->create([
	    		'outlet_id'	=> $item->options->outlet_id,
	    		'amount'	=> $item->subtotal(),
	    		'address'	=> $request->address,
	    		'city'	=> $request->city,
	    		'country'	=> $request->country,
	    		'phone'	=> $request->phone,
	    		'email'	=> $request->email,
	    	]);

	    	$order->details()->create([
	    		'product_id'	=> $item->id,
	    		'name'	=> $item->name,
	    		'price'	=> $item->price,
	    		'quantity'	=> $item->qty,
	    	]);
    	}
    }
}

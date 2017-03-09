<?php

namespace App\Http\Controllers\Api\User;

use App\Post;
use Illuminate\Http\Request;
use App\CityCard\ShoppingCart;
use App\Http\Controllers\Controller;

class PurchaseDealsController extends Controller
{
	protected $cart;

	public function __construct(ShoppingCart $cart)
	{
		$this->cart = $cart;
	}

    public function store(Request $request, Post $post)
    {
    	return $this->cart->add($post, [
    		'outlet_id' => $request->outlet_id
    	]);
    }
}

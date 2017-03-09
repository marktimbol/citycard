<?php

namespace App\CityCard;

use App\Post;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCart
{
	public function add(Post $post, $options = [])
	{
		return Cart::add($post->id, $post->title, 1, $post->price, $options);
	}

	public function content()
	{
		return Cart::content();
	}

	public function count()
	{
		return Cart::count();
	}

}
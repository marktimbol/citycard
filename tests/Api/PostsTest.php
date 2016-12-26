<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_api_view_all_posts()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'Dubai Mall'
    	]);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'Dubai Mall',
    		'has_reservation'	=> true,
    	]);

    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id,
    		'title'	=> 'Burj Khalifa - At the Top'
    	]);

    	$outlet->posts()->attach($post->id);

    	$this->createItemForReservation([
    		'outlet_id'	=> $outlet->id,
    		'title'	=> 'Burj Khalifa - At the Top',
    	]);

    	$this->json('GET', 'api/posts')
    		->seeJson([
    			'for_reservation'	=> true,
    		]);
    }
}

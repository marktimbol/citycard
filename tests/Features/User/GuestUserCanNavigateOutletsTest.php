<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestUserCanNavigateOutletsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_guest_user_can_view_all_the_available_outlets()
    {
		$outlets = $this->createOutlets(3);

		$this->json('GET', '/api/outlets');
		foreach( $outlets as $outlet ) {
			$this->seeJson([
				'name'	=> $outlet->name
			]);
		}
    }

    public function test_guest_can_view_single_outlet()
    {
		$outlet = $this->createOutlet([
			'name'	=> 'McDonalds Arabia'
		]);

		$url = sprintf('/api/outlets/%s', $outlet->id);
		$this->json('GET', $url)
			->seeJson([
				'name'	=> $outlet->name
			]);	
    }

    public function test_guest_can_view_all_the_posts_of_an_outlet()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'McDonalds',
    	]);
    	$post = $this->createPost([
    		'title'	=> 'New Big Mac'
    	]);

    	$outlet->posts()->attach($post);

    	$url = sprintf('/api/outlets/%s/posts', $outlet->id);

		$this->json('GET', $url)
				->see($post->title);
    }

    public function test_guest_can_view_certai_post_of_an_outlet()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'McDonalds',
    	]);

    	$post = $this->createPost([
    		'title'	=> 'New Big Mac'
    	]);

    	$outlet->posts()->attach($post);

    	$url = sprintf('/api/outlets/%s/posts/%s', $outlet->id, $post->id);

		$this->json('GET', $url)
			->see($post->title);	
    }

    public function test_guest_cannot_purchase_an_offer_from_an_outlet()
    {
    	$outlet = $this->createOutlet();
    	$post = $this->createPost();
    	$outlet->posts()->attach($post);

    	$url = sprintf('/api/posts/%s/purchase', $post->id);
    	
    	$response = $this->json('POST', $url, []);
    	dd($response);

    	

    }

}

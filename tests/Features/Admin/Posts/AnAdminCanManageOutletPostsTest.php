<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanManageOutletPostsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin();
	}
	
    public function test_an_admin_can_create_a_post_for_the_outlet()
    {
        $outlet = $this->createOutlet();
        
        $url = sprintf('/dashboard/outlets/%s/posts/create', $outlet->id);
        $this->visit($url)
            ->see('Add New Post');
    }

    public function test_an_admin_can_store_a_post_for_the_outlet()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        
        $url = sprintf('/dashboard/outlets/%s/posts/create', $outlet->id);
        $this->visit($url)
            ->select('notification', 'type')
            ->type('The Title', 'title')
            ->type('49', 'price')
            ->type('The description', 'desc')
            ->type('http://google.com', 'link')
            ->select('cashback', 'payment_option')
            ->type('100', 'points')
            ->press('Save')

            ->seeInDatabase('posts', [
                'merchant_id'   => $merchant->id,
                'type'  => 'notification',
                'title' => 'The Title',
                'slug'  => 'the-title',
                'price'  => 49,
                'desc'=> 'The description',
                'link'  => 'http://google.com',
                'payment_option'  => 'cashback',
                'points'  => 100,
                'approved'  => 0
            ])

            ->seeInDatabase('outlet_posts', [
                'outlet_id' => $outlet->id,
                'post_id'   => 1
            ]);

            // ->seePageIs('/dashboard/outlets/'.$outlet->id.'/posts/1');
    }

    public function test_an_admin_can_delete_post_from_an_outlet()
    {
    	$merchant = $this->createMerchant();
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);
    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id
    	]);
    	$outlet->posts()->attach($post);

    	$this->seeInDatabase('outlet_posts', [
    		'outlet_id'	=> $outlet->id,
    		'post_id'	=> $post->id
    	]);

    	$endpoint = sprintf('/dashboard/outlets/%s/posts/%s', $outlet->id, $post->id);
    	$response = $this->delete($endpoint);

		$this->dontSeeInDatabase('outlet_posts', [
			'outlet_id'	=> $outlet->id,
			'post_id'	=> $post->id,
		]);
    }
}

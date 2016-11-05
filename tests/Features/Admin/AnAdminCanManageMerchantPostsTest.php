<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanManageMerchantPostsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin();
	}

    public function test_an_admin_can_view_all_the_posts_of_a_merchant()
    {
        $merchant = $this->createMerchant();
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);

        $url = sprintf('/dashboard/merchants/%s/posts', $merchant->id);
        $this->visit($url)
            ->see($post->title);
    }

    public function test_an_admin_can_add_a_post_to_a_merchant()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'McDonalds'
    	]);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

    	$url = sprintf('/dashboard/merchants/%s/posts/create', $merchant->id);
    	$this->visit($url)
            ->select('notification', 'type')
    		->select('1', 'outlet_ids')
    		->type('Post Title', 'title')
    		->type('Post description', 'description')
    		->press('Save')

    		->seeInDatabase('posts', [
    			'merchant_id'	=> $merchant->id,
                'type'  => 'notification',
                'title' => 'Post Title',
                'description'   => 'Post description'
    		])

    		->seeInDatabase('outlet_posts', [
    			'outlet_id'	=> $outlet->id,
    			'post_id'	=> 1,
    		]);
    }
}

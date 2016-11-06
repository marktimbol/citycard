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
            ->type('http://google.com', 'link')
    		->press('Save')

    		->seeInDatabase('posts', [
    			'merchant_id'	=> $merchant->id,
                'type'  => 'notification',
                'title' => 'Post Title',
                'slug'  => 'post-title',
                'description'   => 'Post description',
                'link'  => 'http://google.com',
    		])

    		->seeInDatabase('outlet_posts', [
    			'outlet_id'	=> $outlet->id,
    			'post_id'	=> 1,
    		]);
    }

    public function test_an_admin_can_view_the_post_of_a_merchant()
    {
        $merchant = $this->createMerchant();
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);

        $url = sprintf('/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);
        $this->visit($url)
            ->see($post->title);
    }

    public function test_an_admin_can_update_a_post_information()
    {
        $merchant = $this->createMerchant();

        $post = $this->createPost([
            'merchant_id'   => $merchant->id,
            'title' => 'Buy 1 take 1',
        ]);

        $marinaBranch = $this->createOutlet([
            'merchant_id'   => $merchant->id,
        ]);

        $jltBranch = $this->createOutlet([
            'merchant_id'   => $merchant->id,
        ]);

        $post->outlets()->attach($jltBranch);

        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => $jltBranch->id,
            'post_id'   => $post->id
        ]);

        $url = sprintf('/dashboard/merchants/%s/posts/%s/edit', $merchant->id, $post->id);
        $this->visit($url)
            ->type('Buy 2 take 1', 'title')
            ->select('offer', 'type')
            ->select($marinaBranch->id, 'outlet_ids')
            ->type('The new description', 'description')
            ->type('http://google.com', 'link')
            ->press('Update')

            ->seeInDatabase('posts', [
                'id'    => $post->id,
                'merchant_id'   => $merchant->id,
                'type'  => 'offer',
                'title' => 'Buy 2 take 1',
                'description'   => 'The new description',
                'link'  => 'http://google.com'
            ])

            ->dontSeeInDatabase('outlet_posts', [
                'outlet_id' => $jltBranch->id,
                'post_id'   => $post->id
            ])

            ->seeInDatabase('outlet_posts', [
                'outlet_id' => $marinaBranch->id,
                'post_id'   => $post->id
            ]);
    }

    public function test_an_admin_can_remove_a_post_information()
    {
        $merchant = $this->createMerchant();
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);
        
        $url = sprintf('/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);

        $this->visit($url)
            ->press('Delete')

            ->dontSeeInDatabase('posts', [
                'id'    => $post->id
            ]);
    }
}

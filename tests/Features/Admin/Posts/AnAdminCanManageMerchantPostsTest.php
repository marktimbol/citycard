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

    public function test_an_admin_can_add_an_external_post_to_a_merchant()
    {
    	$merchant = $this->createMerchant([
    		'name'	=> 'McDonalds'
    	]);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

		$source = $this->createExternal([
			'name'	=> 'Cobone'
		]);

        $endpoint = sprintf('/dashboard/merchants/%s/posts', $merchant->id);
		$response = $this->post($endpoint, [
			'source'	=> 'external',
			'source_from'	=> $source->id,
			'source_link'	=> 'http://google.com',
			'type'	=> 'offer',
			'outlet_ids'	=> ['1'],
			'title'	=> 'The Title',
			'price'	=> 49,
			'desc'	=> 'The description',
		]);

		dd($response);

        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
			'source'	=> 'external',
			'source_link'	=> 'http://google.com',
            'type'  => 'offer',
            'title' => 'The Title',
            'slug'  => 'the-title',
            'price'  => 49,
            'desc'=> 'The description',
            'approved'  => 0
        ])

        ->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ])

		->seeInDatabase('external_posts', [
			'external_id'	=> $source->id,
			'post_id'	=> 1
		]);

        // ->seePageIs('/dashboard/merchants/'.$merchant->id.'/posts/1');
    }

    public function test_an_admin_can_view_the_post_of_a_merchant()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->posts()->save($post);

        $url = sprintf('/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);
        $this->visit($url)
            ->see($post->title)
            ->see('AED ' . $post->price)
            ->see($post->description)
            ->see($post->link)
            ->see('Cashback & Points')
            ->see($post->points);
    }

    public function test_an_admin_can_update_a_post_information()
    {
        $merchant = $this->createMerchant();

        $post = $this->createPost([
            'merchant_id'   => $merchant->id,
            'title' => 'Buy 1 take 1',
            'price' => 49
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
            ->select('offer', 'type')
            ->select($marinaBranch->id, 'outlet_ids')
            ->type('Buy 2 take 1', 'title')
            ->type('59', 'price')
            ->type('The new description', 'desc')
            ->type('http://google.com', 'link')
            ->select('cashback', 'payment_option')
            ->type('200', 'points')
            ->press('Update')

            ->seeInDatabase('posts', [
                'id'    => $post->id,
                'merchant_id'   => $merchant->id,
                'type'  => 'offer',
                'title' => 'Buy 2 take 1',
                'price' => 59,
                'desc'   => 'The new description',
                'link'  => 'http://google.com',
                'payment_option'    => 'cashback',
                'points'    => 200
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
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->posts()->save($post);

        $url = sprintf('/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);

        $this->visit($url)
            ->press('Delete')

            ->dontSeeInDatabase('posts', [
                'id'    => $post->id
            ]);
    }
}

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

        $url = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $this->visit($url)
            ->see($post->title);
    }

    public function test_an_admin_can_add_an_external_post_to_a_merchant()
    {
		$area = $this->createArea([
			'name'	=> 'Al Barsha'
		]);

    	$merchant = $this->createMerchant([
    		'name'	=> 'McDonalds'
    	]);

		$area->merchants()->attach($merchant);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

		$this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

		$source = $this->createSource([
			'name'	=> 'Cobone'
		]);

		$category = $this->createCategory([
			'name'	=> 'Food',
		]);

		$buffet = $this->createSubcategory([
			'category_id'	=> $category->id,
			'name'	=> 'Buffet'
		]);

		$brunch = $this->createSubcategory([
			'category_id'	=> $category->id,
			'name'	=> 'Brunch'
		]);

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
		$request = $this->post($endpoint, [
            'source'    => 'external',
			'isExternal'	=> true,
			'source_from'	=> $source->id,
			'source_link'	=> 'http://google.com',

			'category'	=> 1,
			'subcategories'	=> '1,2',

			'type'	=> 'notification',
			'outlet_ids'	=> '1,2',
			'title'	=> 'The Title',
			'desc'	=> 'The description',
		]);
        
        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,
            'type'  => 'notification',
            'title' => 'The Title',
            'slug'  => 'the-title',
            'desc'=> 'The description',
			'isExternal'	=> true,
            'approved'  => false
        ])

		->seeInDatabase('subcategory_posts', [
			'subcategory_id'	=> $buffet->id,
			'post_id'	=> 1,
		])

		->seeInDatabase('subcategory_posts', [
			'subcategory_id'	=> $brunch->id,
			'post_id'	=> 1
		])

		->seeInDatabase('source_posts', [
			'source_id'	=> $source->id,
			'post_id'	=> 1,
			'link'	=> 'http://google.com'
		])

        ->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ])

        ->seeInDatabase('admin_posts', [
            'admin_id'  => 1,
            'post_id'   => 1,
        ]);
    }

    public function test_an_admin_can_add_an_external_post_to_a_merchant_with_custom_options()
    {
        $area = $this->createArea([
            'name'  => 'Al Barsha'
        ]);

        $merchant = $this->createMerchant([
            'name'  => 'McDonalds'
        ]);

        $area->merchants()->attach($merchant);

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $source = $this->createSource([
            'name'  => 'Cobone'
        ]);

        $category = $this->createCategory([
            'name'  => 'Food',
        ]);

        $buffet = $this->createSubcategory([
            'category_id'   => $category->id,
            'name'  => 'Buffet'
        ]);

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $response = $this->post($endpoint, [
            'source'    => 'external',
            'isExternal'    => true,
            'source_from'   => $source->id,
            'source_link'   => 'http://google.com',

            'category'  => 1,
            'subcategories' => '1,Brunch',

            'type'  => 'notification',
            'outlet_ids'    => '1,2',
            'title' => 'The Title',
            'desc'  => 'The description',
        ]);

        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,
            'type'  => 'notification',
            'title' => 'The Title',
            'slug'  => 'the-title',
            'desc'=> 'The description',
            'isExternal'    => true,
            'approved'  => false
        ])

        ->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => $buffet->id,
            'post_id'   => 1,
        ])

        ->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 2,
            'post_id'   => 1
        ])

        ->seeInDatabase('source_posts', [
            'source_id' => $source->id,
            'post_id'   => 1,
            'link'  => 'http://google.com'
        ])

        ->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
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
			'type'	=> 'notification',
            'merchant_id'   => $merchant->id
        ]);
        $outlet->posts()->save($post);

        $url = sprintf(adminPath() . '/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);
        $this->visit($url)
            ->see('Notification')
            ->see($post->title)
            ->see($post->desc);
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

        $url = sprintf(adminPath() . '/dashboard/merchants/%s/posts/%s/edit', $merchant->id, $post->id);
        $this->visit($url)
            ->select('offer', 'type')
            ->select($marinaBranch->id, 'outlet_ids')
            ->type('Buy 2 take 1', 'title')
            ->type('The new description', 'desc')
            ->press('Update')

            ->seeInDatabase('posts', [
                'id'    => $post->id,
                'merchant_id'   => $merchant->id,
                'type'  => 'offer',
                'title' => 'Buy 2 take 1',
                'desc'   => 'The new description',
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

    public function test_an_authorized_can_remove_a_post_information()
    {
        $merchant = $this->createMerchant();
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);
        $post = $this->createPost([
            'merchant_id'   => $merchant->id
        ]);
        $outlet->posts()->save($post);

        $url = sprintf(adminPath() . '/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);

        $this->visit($url)
            ->press('Delete')

            ->dontSeeInDatabase('posts', [
                'id'    => $post->id
            ]);
    }
}

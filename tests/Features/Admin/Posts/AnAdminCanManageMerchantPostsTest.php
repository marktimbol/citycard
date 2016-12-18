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

		$this->adminSignIn();
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
        $country = $this->createCountry([
            'name'  => 'UAE'
        ]);

        $city = $this->createCity([
            'country_id'    => $country->id,
            'name'  => 'Dubai'
        ]);

		$area = $this->createArea([
            'city_id'   => $city->id,
			'name'	=> 'Al Barsha'
		]);

    	$merchant = $this->createMerchant([
    		'name'	=> 'McDonalds'
    	]);

		$area->merchants()->attach($merchant);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id
    	]);

        $area->outlets()->attach($outlet);

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
        ]);

		$this->seeInDatabase('subcategory_posts', [
			'subcategory_id'	=> $buffet->id,
			'post_id'	=> 1,
		])

    		->seeInDatabase('subcategory_posts', [
    			'subcategory_id'	=> $brunch->id,
    			'post_id'	=> 1
    		]);

		$this->seeInDatabase('source_posts', [
			'source_id'	=> $source->id,
			'post_id'	=> 1,
			'link'	=> 'http://google.com'
		]);

        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ]);

        $this->seeInDatabase('country_posts', [
            'country_id'    => 1,
            'post_id'   => 1,
        ])
            ->seeInDatabase('city_posts', [
                'city_id'   => 1,
                'post_id'   => 1,
            ])
            ->seeInDatabase('area_posts', [
                'area_id'   => 1,
                'post_id'   => 1,
            ]);        

        $this->seeInDatabase('admin_posts', [
            'admin_id'  => 1,
            'post_id'   => 1,
        ]);
    }

    public function test_an_admin_can_add_an_external_event_post_to_a_merchant()
    {
        $area = $this->createArea([
            'name'  => 'Al Barsha'
        ]);

        $merchant = $this->createMerchant([
            'name'  => 'Platinum LLC'
        ]);

        $area->merchants()->attach($merchant);

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id,
        ]);

        $source = $this->createSource([
            'name'  => 'Cobone'
        ]);

        $category = $this->createCategory([
            'name'  => 'Concert',
        ]);

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $request = $this->post($endpoint, [
            'source'    => 'external',
            'isExternal'    => true,
            'source_from'   => $source->id,
            'source_link'   => 'http://google.com',

            'category'  => 1,
            'subcategories' => 'Concert',

            'type'  => 'events',
            'event_date'    => 'soon',
            'event_time'    => 'later',
            
            'outlet_ids'    => '1',
            'title' => 'The Concert',
            'desc'  => '1 Day concert',
        ]);
        
        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,
            
            'type'  => 'events',
            'event_date'  => 'soon',
            'event_time'  => 'later',

            'title' => 'The Concert',
            'slug'  => 'the-concert',
            'desc'=> '1 Day concert',

            'isExternal'    => true,
            'approved'  => false
        ]);

        $this->seeInDatabase('subcategories', [
            'category_id'   => $category->id,
            'name'  => 'Concert',
        ])
            ->seeInDatabase('subcategory_posts', [
                'subcategory_id'    => 1,
                'post_id'   => 1,
            ]);

        $this->seeInDatabase('source_posts', [
            'source_id' => $source->id,
            'post_id'   => 1,
            'link'  => 'http://google.com'
        ]);

        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ]);

        $this->seeInDatabase('admin_posts', [
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

        $this->createSubcategory([
            // 'id'    => 1,
            'category_id'   => $category->id,
            'name'  => 'Buffet'
        ]);

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $request = $this->post($endpoint, [
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
        ]);

        $this->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 1,
            'post_id'   => 1,
        ])

        ->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 2,
            'post_id'   => 1
        ]);

        $this->seeInDatabase('source_posts', [
            'source_id' => $source->id,
            'post_id'   => 1,
            'link'  => 'http://google.com'
        ]);

        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ]);

        // ->seePageIs('/dashboard/merchants/'.$merchant->id.'/posts/1');
    }    

    public function test_an_admin_can_add_an_external_post_to_a_merchant_with_more_custom_options()
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

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $request = $this->post($endpoint, [
            'source'    => 'external',
            'isExternal'    => true,
            'source_from'   => $source->id,
            'source_link'   => 'http://google.com',

            'category'  => 1,
            'subcategories' => 'Filipino Food,Indian Food,Italian Food',

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
            'subcategory_id'    => 1,
            'post_id'   => 1,
        ])

        ->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 2,
            'post_id'   => 1
        ])

        ->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 3,
            'post_id'   => 1
        ])        

        ->seeInDatabase('subcategories', [
            'id'    => 1,
            'name'  => 'Filipino Food',
        ])

        ->seeInDatabase('subcategories', [
            'id'    => 2,
            'name'  => 'Indian Food',
        ])        

        ->seeInDatabase('subcategories', [
            'id'    => 3,
            'name'  => 'Italian Food',
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

    public function test_an_authorized_person_can_view_all_the_published_posts()
    {
        $published = $this->createPost([
            'title' => 'Buy 1 take 1',
            'published' => true,
        ]);

        $forReview = $this->createPost([
            'title' => 'Buy 1 take 10',
            'published' => false,
        ]);        

        $this->visit(adminPath() . '/dashboard/posts?view=published')
            ->see('Buy 1 take 1')
            ->dontSee('Buy 1 take 10');
    }    

    public function test_an_authorized_person_can_view_all_the_unpublished_posts()
    {
        $published = $this->createPost([
            'title' => 'Buy 1 take 1',
            'published' => true,
        ]);

        $forReview = $this->createPost([
            'title' => 'Buy 1 take 10',
            'published' => false,
        ]);  

        $this->visit(adminPath() . '/dashboard/posts?view=for-review')
            ->see('Buy 1 take 10')
            ->see('Buy 1 take 1');
    }    

    public function test_an_authorized_person_can_publish_multiple_posts()
    {
        $this->createPost([
            'title' => 'Buy 1 take 1',
            'published' => false,
        ]);  

        $this->createPost([
            'title' => 'Buy 1 take 2',
            'published' => false,
        ]);  

        $this->createPost([
            'title' => 'Buy 1 take 3',
            'published' => false,
        ]);    

        $this->seeInDatabase('posts', [
            'id'    => 1,
            'published' => false,
        ])
            ->seeInDatabase('posts', [
                'id'    => 2,
                'published' => false,
            ])     
            ->seeInDatabase('posts', [
                'id'    => 3,
                'published' => false,
            ]);                       

        $endpoint = adminPath() . '/dashboard/posts/publish';
        $request = $this->post($endpoint, [
            'action'    => 'publish',
            'posts' => [1,2,3]
        ]);

        $this->seeInDatabase('posts', [
            'id'    => 1,
            'published' => true,
        ])
            ->seeInDatabase('posts', [
                'id'    => 2,
                'published' => true,
            ])     
            ->seeInDatabase('posts', [
                'id'    => 3,
                'published' => true,
            ]);                
    }  

    public function test_an_authorized_person_can_unpublish_multiple_posts()
    {
        $this->createPost([
            'title' => 'Buy 1 take 1',
            'published' => true,
        ]);  

        $this->createPost([
            'title' => 'Buy 1 take 2',
            'published' => true,
        ]);  

        $this->createPost([
            'title' => 'Buy 1 take 3',
            'published' => true,
        ]);    

        $this->seeInDatabase('posts', [
            'id'    => 1,
            'published' => true,
        ])
            ->seeInDatabase('posts', [
                'id'    => 2,
                'published' => true,
            ])     
            ->seeInDatabase('posts', [
                'id'    => 3,
                'published' => true,
            ]);                       

        $endpoint = adminPath() . '/dashboard/posts/unpublish';
        $request = $this->post($endpoint, [
            'action'    => 'unpublish',
            'posts' => [1,2,3]
        ]);

        $this->seeInDatabase('posts', [
            'id'    => 1,
            'published' => false,
        ])
            ->seeInDatabase('posts', [
                'id'    => 2,
                'published' => false,
            ])     
            ->seeInDatabase('posts', [
                'id'    => 3,
                'published' => false,
            ]);                
    }       

    public function method()
    {
        $endpoint = sprintf('%s/dashboard/posts/%s/approve', adminpath(), $post->id);
        $request = $this->post($endpoint);

        $this->visit(adminPath() . '/dashboard/posts')
            ->see('Buy 1 take 1');

        $this->seeInDatabase('posts', [
            'id'    => $post->id,
            'approved'  => true,
        ]);        
    }
}

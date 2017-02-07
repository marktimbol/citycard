<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
		$request = $this->post($endpoint, [
            'source'    => 'external',
			'isExternal'	=> true,
			'source_from'	=> $source->id,
			'source_link'	=> 'http://google.com',

			'category'	=> 1,
			'subcategories'	=> [
                0 => [
                    'value' => 'Buffet',
                ],
                1 => [
                    'value' => 'Custom subcategory'
                ]
            ],
			'type'	=> 'deals',
			'outlet_ids'	=> '1,2',
			'title'	=> 'The Title',
			'desc'	=> 'The description',
		]);

        $this->seeInDatabase('posts', [
            'id'    => 1,
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,
            'type'  => 'deals',
            'title' => 'The Title',
            'slug'  => 'the-title',
            'desc'=> 'The description',
			'isExternal'	=> true,
            'published'  => true
        ]);

        $this->seeInDatabase('subcategories', [
            'category_id'   => $category->id,
            'name'  => 'Buffet'
        ])
            ->seeInDatabase('subcategories', [
                'category_id'   => $category->id,
                'name'  => 'Custom subcategory'
            ]);

		$this->seeInDatabase('subcategory_posts', [
			'subcategory_id'	=> $buffet->id,
			'post_id'	=> 1,
		])
    		->seeInDatabase('subcategory_posts', [
    			'subcategory_id'	=> 2,
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

    public function test_an_admin_can_add_an_internal_post_to_a_merchant_and_allow_the_post_to_be_reserve_by_users()
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
            'name'  => 'Downtown Dubai'
        ]);

        $merchant = $this->createMerchant([
            'name'  => 'Dubai Mall'
        ]);

        $area->merchants()->attach($merchant);

        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id
        ]);

        $area->outlets()->attach($outlet);

        $category = $this->createCategory([
            'name'  => 'Travel',
        ]);

        $endpoint = sprintf(adminPath() . '/dashboard/merchants/%s/posts', $merchant->id);
        $request = $this->post($endpoint, [
            'source'    => 'citycard',
            'isExternal'    => false,

            'category'  => 1,
            'subcategories' => 'Visit Dubai',

            'type'  => 'deals',
            'outlet_ids'    => '1',
            'title' => 'Burj Khalifa - At the Top',
            'desc'  => 'View the world',

            'allow_for_reservation'   => true,
        ]);

        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,
            'type'  => 'deals',
            'title' => 'Burj Khalifa - At the Top',
            'desc'=> 'View the world',
            'isExternal'    => false,
            'published'  => true
        ]);

        $this->seeInDatabase('subcategory_posts', [
            'subcategory_id'    => 1,
            'post_id'   => 1,
        ]);

        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => 1,
            'post_id'   => 1
        ])
            ->seeInDatabase('item_for_reservations', [
                'outlet_id' => 1,
                'title' => 'Burj Khalifa - At the Top'
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
            'event_date'    => date('Y-m-d'),
            'event_time'    => 'later',
            'event_location'    => 'Event Location',

            'outlet_ids'    => '1',
            'title' => 'The Concert',
            'desc'  => '1 Day concert',
        ]);

        $this->seeInDatabase('posts', [
            'merchant_id'   => $merchant->id,
            'category_id'   => $category->id,

            'type'  => 'events',
            'event_date'  => date('Y-m-d') . ' ' . '00:00:00',
            'event_time'  => 'later',
            'event_location'   => 'Event Location',
            
            'title' => 'The Concert',
            'slug'  => 'the-concert',
            'desc'=> '1 Day concert',

            'isExternal'    => true,
            'published'  => true
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
        $starbucks = $this->createMerchant([
            'name'  => 'Starbucks'
        ]);

        $outlet = $this->createOutlet([
            'merchant_id'   => $starbucks->id,
            'name'  => 'Starbucks - Al Ghurair Centre'
        ]);

        $cobone = $this->createSource([
            'name'  => 'Cobone'
        ]);

        $food = $this->createCategory([
            'name'  => 'Food'
        ]);

        $tea = $this->createSubcategory([
            'category_id'   => $food->id,
            'name'  => 'Tea'
        ]);

        $post = $this->createPost([
            'merchant_id'   => $starbucks->id,
            'category_id'   => $food->id,
            'type'  => 'deals',
            'title' => 'Buy 1 get 1 coffee',
            'slug'  => 'buy-1-get-1-coffee',
            'desc'  => 'Buy 1 get 1 coffee offer',
            'isExternal'    => false,
        ]);

        $post->outlets()->attach($outlet);
        $post->subcategories()->attach($tea);
        $post->sources()->attach($cobone, [
            'link'  => 'http://cobone.com/starbucks'
        ]);

        $dcc_branch = $this->createOutlet([
            'merchant_id'   => $starbucks->id,
            'name'  => 'Starbucks - City Centre Deira'
        ]);

        $event_date = Carbon::tomorrow()->toDateTimeString();

        $coffee_shop = $this->createCategory([
            'name'  => 'Coffee Shop'
        ]);

        $request = $this->put(adminPath() . "/dashboard/merchants/$starbucks->id/posts/$post->id", [
            'title' => 'Buy 2 get 1',
            'desc'  => 'Updated: Buy 2 get 1',
            // Update Source (from cobone to groupon)
            'source_from'   => 'Groupon',
            'source_link'    => 'http://groupon.ae/starbucks',
            // Update Post Type
            'type'  => 'events',
            'event_date'    => $event_date,
            'event_time'    => '9:00 pm',
            'event_location'    => 'Dubai - United Arab Emirates',
            'outlets'   => [
                0   => [
                    'value' => $outlet->id,
                ],
                1   => [
                    'value' => $dcc_branch->id,
                ]
            ],            
            // Update Post categories
            'category'  => $coffee_shop->id,
            'subcategories' => [
                0 => [
                    'value' => 'Coffee',
                ],
                1 => [
                    'value' => 'Cake',
                ]
            ],
        ]);

        $this->seeInDatabase('posts', [
            'id'    => $post->id,
            'title' => 'Buy 2 get 1',
            'desc'  => 'Updated: Buy 2 get 1',
            'type'  => 'events',
            'event_date'    => $event_date,
            'event_time'    => '9:00 pm',
            'event_location'    => 'Dubai - United Arab Emirates',            
        ]);
        // We have to remove the existing source
        // from the post since we selected different
        // source during update.
        $this->dontSeeInDatabase('source_posts', [
            'source_id' => $cobone->id,
            'post_id'   => $post->id,
        ])
            // We should see the newly created
            // source (groupon) in our database.
            ->seeInDatabase('sources', [
                'name'  => 'Groupon'
            ])
            // We have to update as well
            // the source of the post
            ->seeInDatabase('source_posts', [
                'source_id' => 2,
                'post_id'   => $post->id,
                'link'  => 'http://groupon.ae/starbucks'
            ]);

        // Update outlets
        $this->seeInDatabase('outlet_posts', [
            'outlet_id' => $outlet->id,
            'post_id'   => $post->id,
        ])       
            // New Outlet     
            ->seeInDatabase('outlet_posts', [
                'outlet_id' => $dcc_branch->id,
                'post_id'   => $post->id,
            ]);

        // Update category & subcategories
        $this->seeInDatabase('categories', [
            'name'  => 'Coffee Shop'
        ])
            ->seeInDatabase('posts', [
                'id'   => $post->id,
                'category_id'    => 2, // Coffee Shop
            ]);

        $this->seeInDatabase('subcategories', [
            'category_id'   => 2, // Coffee Shop
            'name'  => 'Coffee'
        ])
            ->seeInDatabase('subcategories', [
                'category_id'   => 2, // Coffee Shop
                'name'  => 'Cake'
            ])
            ->seeInDatabase('subcategory_posts', [
                'subcategory_id'    => 2, // Coffee
                'post_id'    => $post->id,
            ])
            ->seeInDatabase('subcategory_posts', [
                'subcategory_id'    => 3, // Cake
                'post_id'    => $post->id,
            ])
            ->dontSeeInDatabase('subcategory_posts', [
                'subcategory_id'    => $tea->id, // Tea
                'post_id'   => $post->id,
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

        $this->delete($url)
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
        $request = $this->delete($endpoint, [
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
}

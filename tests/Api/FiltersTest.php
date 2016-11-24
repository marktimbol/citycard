<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FiltersTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_filters_posts_by_city()
    {
        // City
        $dubai = $this->createCity([
            'name'  => 'Dubai'
        ]);

        // Area
        $alRiggaArea = $this->createArea([
            'city_id'   => $dubai->id,
            'name'  => 'Al Rigga'
        ]);

        // Merchant
        $zara = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        // Zara in Al Rigga
        $zaraAlRigga = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $alRiggaArea->outlets()->attach($zaraAlRigga);

        $post = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara Offer in Dubai'
        ]);
        $zaraAlRigga->posts()->attach($post);


        // Abu Dhabi
        $abuDhabi = $this->createCity([
            'name'  => 'Abu Dhabi'
        ]);

        // Area
        $abuDhabiArea = $this->createArea([
            'city_id'   => $abuDhabi->id,
            'name'  => 'Abu Dhabi Area'
        ]);

        // Zara in Al Rigga
        $zaraAbuDhabi = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Abu Dhabi'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $abuDhabiArea->outlets()->attach($zaraAbuDhabi);

        $post2 = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara Offer in Abu Dhabi'
        ]);
        $zaraAbuDhabi->posts()->attach($post2);

        $endpoint = sprintf('/api/filters');
        $response = $this->json('GET', $endpoint, [
            'city_id' => $dubai->id
        ])
        ->seeJson([
            'title' => 'Zara Offer in Dubai'
        ])
        ->dontSee('Zara Offer in Abu Dhabi');
    }

    public function test_it_filters_posts_by_specific_area()
    {
        // Area
        $area = $this->createArea([
            'name'  => 'Al Barsha'
        ]);

        // Merchant
        $merchant = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        // Zara in Al Barsha
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Barsha'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $area->outlets()->attach($outlet);

        $post = $this->createPost([
            'merchant_id'   => $merchant->id,
            'title' => 'Zara Offer in Al Rigga Area'
        ]);
        $outlet->posts()->attach($post);

        $endpoint = sprintf('/api/filters');
        $this->json('GET', $endpoint, [
            'city_id'   => '',
            'area_ids' => [$area->id]
        ])
        ->seeJson([
            'title' => 'Zara Offer in Al Rigga Area'
        ]);
    }

    public function test_it_filters_posts_by_multiple_areas()
    {
        $city = $this->createCity([
            'name'  => 'Dubai'
        ]);

        // Area
        $area = $this->createArea([
            'city_id'  => $city->id,
            'name'  => 'Al Barsha'
        ]);

        // First merchant
        $merchant = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        // Zara in Al Barsha
        $outlet = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Barsha'
        ]);

        $area->outlets()->attach($outlet);

        $post = $this->createPost([
            'merchant_id'   => $merchant->id,
            'title' => 'Show this post'
        ]);
        $outlet->posts()->attach($post);

        // Area
        $area2 = $this->createArea([
            'city_id'  => $city->id,
            'name'  => 'Al Rigga'
        ]);

        // First merchant
        $merchant2 = $this->createMerchant([
            'name'  => 'Pull and Bear'
        ]);

        // Zara in Al Barsha
        $outlet2 = $this->createOutlet([
            'merchant_id'   => $merchant2->id,
            'name'  => 'Pull and Bear - Al Rigga'
        ]);

        $area2->outlets()->attach($outlet2);

        $post2 = $this->createPost([
            'merchant_id'   => $merchant2->id,
            'title' => 'More post'
        ]);
        $outlet2->posts()->attach($post2);

        $endpoint = sprintf('/api/filters');
        $response = $this->json('GET', $endpoint, [
            'area_ids' => [$area->id, $area2->id]
        ])
        ->seeJson([
            'title' => 'Show this post'
        ])
        ->seeJson([
            'title' => 'More post'
        ]);
    }

    public function test_it_filters_posts_by_categories_on_the_selected_city()
    {
        // Setup the location
        $city = $this->createCity([
            'name'  => 'Dubai'
        ]);

        $area = $this->createArea([
            'name'  => 'Al Barsha',
        ]);

        // Setup the merchant
        $merchant = $this->createMerchant([
            'name'  => 'The Mechant'
        ]);

        $area->merchants()->attach($merchant);

        $outlet = $this->createOutlet([
            'name'  => 'The Outlet'
        ]);

        $merchant->outlets()->save($outlet);

        // Setup the categories
        $foodCategory = $this->createCategory([
            'name'  => 'Category'
        ]);

        $beautyCategory = $this->createCategory([
            'name'  => 'Beauty'
        ]);

        // Setup the posts
        $postFood = $this->createPost([
            'merchant_id'   => $merchant->id,
            'category_id'   => $foodCategory->id,
            'title' => 'Post about Food',
        ]);

        $postBeauty = $this->createPost([
            'merchant_id'   => $merchant->id,
            'category_id'   => $beautyCategory->id,
            'title' => 'Post about Beauty',
        ]);

        $outlet->posts()->attach($postFood);
        $outlet->posts()->attach($postBeauty);

        $response = $this->json('GET', '/api/filters', [
            'city_id'  => $city->id,
            'area_ids'  => [$area->id],
            'categories'  => [$foodCategory->id],
        ])
        ->seeJson([
            'title' => 'Post about Food'
        ])
        ->dontSeeJson([
            'title' => 'Post about Beauty'
        ]);
    }


}

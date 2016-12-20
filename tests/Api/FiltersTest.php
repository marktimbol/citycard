<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FiltersTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_shows_all_posts_by_city()
    {
        // City
        $dubai = $this->createCity([
            'name'  => 'Dubai'
        ]);

        // Area
        $alRigga = $this->createArea([
            'city_id'   => $dubai->id,
            'name'  => 'Al Rigga'
        ]);

        // Merchant
        $zara = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        // Zara in Al Rigga
        $zaraOutletDubai = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $alRigga->outlets()->attach($zaraOutletDubai);

        $post = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara Offer in Dubai',
        ]);

        $zaraOutletDubai->posts()->attach($post);

        // Abu Dhabi
        $abuDhabi = $this->createCity([
            'name'  => 'Abu Dhabi'
        ]);

        // Area
        $khalifaCity = $this->createArea([
            'city_id'   => $abuDhabi->id,
            'name'  => 'Khalifa City'
        ]);

        // Zara in Al Rigga
        $zaraOutletAbuDhabi = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Abu Dhabi'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $khalifaCity->outlets()->attach($zaraOutletAbuDhabi);

        $post2 = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara Offer in Abu Dhabi',
            'published' => true,
        ]);
        $zaraOutletAbuDhabi->posts()->attach($post2);

        $request = $this->json('GET', '/api/posts', [
            'filter'   => true,
            'city' => '1',
            'areas' => '',
            'categories'    => ''
        ]);

        $this->seeJson([
            'title' => 'Zara Offer in Dubai'
        ])
        ->dontSee('Zara Offer in Abu Dhabi');
    }

    public function test_it_show_all_the_posts_by_specific_area()
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

        $endpoint = sprintf('/api/posts');
        $request = $this->json('GET', $endpoint, [
            'filter'    => true,
            'city'   => '',
            'areas' => '1',
            'categories' => ''
        ]);

        $this->seeJson([
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

        $endpoint = sprintf('/api/posts');
        $response = $this->json('GET', $endpoint, [
            'filters'   => true,
            'city'  => '',
            'areas' => '1,2',
            'categories'    => '',
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
            'city_id'   => $city->id,
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
        $area->outlets()->attach($outlet);

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

        $response = $this->json('GET', '/api/posts', [
            'filter'    => true,
            'city'  => '1',
            'areas'  => '',
            'categories'  => '1'
        ])
        ->seeJson([
            'title' => 'Post about Food'
        ])
        ->dontSeeJson([
            'title' => 'Post about Beauty'
        ]);
    }

    public function test_it_shows_all_the_posts_in_multiple_areas_filtered_by_categories()
    {
        // ====================================
        $city = $this->createCity([
            'name'  => 'Dubai'
        ]);

        // ====================================
        $alRigga = $this->createArea([
            'city_id'   => $city->id,
            'name'  => 'Al Rigga',
        ]);

        $burjuman = $this->createArea([
            'city_id'   => $city->id,
            'name'  => 'Burjuman',
        ]);

        $karama = $this->createArea([
            'city_id'   => $city->id,
            'name'  => 'Karama',
        ]);

        // ====================================

        $merchant = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        $alRigga->merchants()->attach($merchant);
        $burjuman->merchants()->attach($merchant);
        $karama->merchants()->attach($merchant);

        // ====================================

        $zaraAlRigga = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        $zaraBurjuman = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Al Burjuman'
        ]);

        $zaraKarama = $this->createOutlet([
            'merchant_id'   => $merchant->id,
            'name'  => 'Zara - Karama'
        ]);

        $alRigga->outlets()->attach($zaraAlRigga);
        $burjuman->outlets()->attach($zaraBurjuman);
        $karama->outlets()->attach($zaraKarama);

        // ====================================

        $clothing = $this->createCategory([
            'name'  => 'Clothing'
        ]);

        $beauty = $this->createCategory([
            'name'  => 'Beauty'
        ]);

        $decorations = $this->createCategory([
            'name'  => 'Decorations'
        ]);

        // ====================================

        $postAboutClothing = $this->createPost([
            'merchant_id'   => $merchant->id,
            'category_id'   => $clothing->id,
            'title' => 'Post about Clothing',
        ]);

        $postAboutBeauty = $this->createPost([
            'merchant_id'   => $merchant->id,
            'category_id'   => $beauty->id,
            'title' => 'Post about Beauty',
        ]);

        $postAboutDecorations = $this->createPost([
            'merchant_id'   => $merchant->id,
            'category_id'   => $decorations->id,
            'title' => 'Post about Decorations',
        ]);

        $zaraAlRigga->posts()->attach($postAboutClothing);
        $zaraBurjuman->posts()->attach($postAboutClothing);
        $zaraKarama->posts()->attach($postAboutClothing);

        $zaraAlRigga->posts()->attach($postAboutBeauty);
        $zaraBurjuman->posts()->attach($postAboutBeauty);
        $zaraKarama->posts()->attach($postAboutBeauty);

        $request = $this->json('GET', 'api/posts', [
            'filter'   => true,
            'city'  => '1',
            'areas' => '1,2,3',
            'categories'    => '1,2',
        ])

        ->seeJson([
            'title' => 'Post about Clothing'
        ])
        ->seeJson([
            'title' => 'Post about Beauty'
        ])
        ->dontSeeJson([
            'title' => 'Post about Decorations'
        ]);
    }
}

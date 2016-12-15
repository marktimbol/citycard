<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttachAllPostsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->adminSignIn();
	}

    public function test_it_attach_all_posts_in_areas_cities_and_countries()
    {
    	$country = $this->createCountry([
    		'name'	=> 'UAE'
    	]);

    	$city = $this->createCity([
    		'country_id'	=> $country->id,
    		'name'	=> 'Dubai'
    	]);

    	$area = $this->createArea([
    		'city_id'	=> $city->id,
    		'name'	=> 'Al Rigga'
    	]);

    	$merchant = $this->createMerchant([
    		'name'	=> 'The Merchant'
    	]);

    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $merchant->id,
    		'name'	=> 'The Outlet'
    	]);

    	$area->merchants()->attach($merchant);

    	$category = $this->createCategory([
    		'name'	=> 'The Category'
    	]);

    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id,
    		'category_id'	=> $category->id,
    		'title'	=> 'The Post'
    	]);

    	$request = $this->json('GET', 'http://admin.citycard.dev/dashboard/attach-posts');

    	$this->seeInDatabase('country_posts', [
    		'country_id'	=> 1,
    		'post_id'	=> 1,
    	])
    		->seeInDatabase('city_posts', [
    			'city_id'	=> 1,
    			'post_id'	=> 1,
    		])
    		->seeInDatabase('area_posts', [
    			'area_id'	=> 1,
    			'post_id'	=> 1,
    		]);

    	$this->seeInDatabase('merchant_posts', [
    		'merchant_id'	=> 1,
    		'post_id'	=> 1,
    	])
    		->seeInDatabase('outlet_posts', [
    			'outlet_id'	=> 1,
    			'post_id'	=> 1,
    		]);

    	$this->seeInDatabase('category_posts', [
    		'category_id'	=> 1,
    		'post_id'	=> 1,
    	]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewThePostsOfTheSelectedSourceTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }
    
    public function test_it_shows_all_the_posts_of_a_specific_source() // eg. Groupon
    {
    	$source = $this->createSource([
    		'name'	=> 'Groupon'
    	]);
    	$this->createSource([
    		'name'	=> 'Cobone'
    	]);

    	$groupon = $this->createPost([
    		'title'	=> 'Groupon post'
    	]);

    	$this->createPost([
    		'title'	=> 'Talabat post'
    	]);    	

    	$url = sprintf('/dashboard/sources/%s/posts', $source->id);
    	$this->visit($url)
    		->see('Groupon post')
    		->dontSee('Talabat post');
    }
}

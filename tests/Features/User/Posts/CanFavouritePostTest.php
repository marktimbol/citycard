<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CanFavouritePostTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->actingAsUser([
			'name'	=> 'John Doe'
		]);
	}

    public function test_a_user_can_favourite_a_post()
    {
    	$user = auth()->guard('user_api')->user();

    	$post = $this->createPost([
    		'title'	=> '50% discount'
    	]);

    	$endpoint = sprintf('/api/posts/%s/favourites', $post->id);
    	$request = $this->json('POST', $endpoint, [
    		'api_token'	=> $user->api_token
    	]);
    	$this->seeInDatabase('user_favourites', [
    		'user_id'	=> $user->id,
    		'post_id'	=> $post->id
    	]);
    }
}

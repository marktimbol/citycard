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

    public function test_a_user_can_unfavourite_a_post()
    {
        $user = auth()->guard('user_api')->user();

        $post = $this->createPost([
            'title' => '50% discount'
        ]);

        $user->favourites()->attach($post);

        $this->seeInDatabase('user_favourites', [
            'user_id'   => $user->id,
            'post_id'   => $post->id
        ]);

        $endpoint = sprintf('/api/posts/%s/favourites/%s', $post->id, $post->id);
        $request = $this->delete($endpoint, [
            'api_token' => $user->api_token
        ]);
        
        $this->dontSeeInDatabase('user_favourites', [
            'user_id'   => $user->id,
            'post_id'   => $post->id
        ]);
    }    

    public function test_a_user_can_view_all_his_or_her_favourited_posts()
    {
        $user = auth()->guard('user_api')->user();

        $post = $this->createPost([
            'title' => '50% discount'
        ]);

        $this->createPost([
            'title' => '0% discount'
        ]);        

        $user->favourites()->attach($post);

        $this->json('GET', 'api/favourites')
            ->seeJson([
                'title' => '50% discount'
            ])
            ->dontSeeJson([
                'title' => '0% discount'
            ]);
    }
}

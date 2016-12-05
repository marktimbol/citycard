<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CannotFavouritePostTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_guest_user_cannot_favourite_a_post()
    {
    	$post = $this->createPost([
    		'title'	=> '50% discount'
    	]);

    	$endpoint = sprintf('/api/posts/%s/favourite', $post->id);
    	$this->json('POST', $endpoint);
    	$this->seeJson([
    		'error'	=> 'Unauthenticated.'
    	]);
    }
}

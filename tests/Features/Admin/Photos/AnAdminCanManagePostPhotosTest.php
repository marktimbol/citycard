<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanManagePostPhotosTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->adminSignIn();
	}

    public function test_an_authorized_can_delete_photo_from_a_post()
    {
        $post = $this->createPost();
    	$photo = $post->photos()->create([
    		'url'	=> 'http://google.com/cover.jpg'
    	]);

    	$this->seeInDatabase('photos', [
    		'url'	=> 'http://google.com/cover.jpg',
    		'imageable_id'	=> $post->id,
    		'imageable_type'	=> 'App\Post'
    	]);

    	$endpoint = sprintf(adminPath() . '/dashboard/posts/%s/photos/%s', $post->id, $photo->id);

    	$request = $this->delete($endpoint);
        
		$this->dontSeeInDatabase('photos', [
			'url'	=> $photo->url,
			'imageable_id'	=> $post->id,
			'imageable_type'	=> 'App\Post'
		]);
    }
}

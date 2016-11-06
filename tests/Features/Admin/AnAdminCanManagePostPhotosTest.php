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
		$this->actingAsAdmin();
	}

    public function test_an_admin_can_upload_photo_to_a_post()
    {
    	$merchant = $this->createMerchant();
    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id
    	]);

    	$endpoint = sprintf('/dashboard/merchants/%s/posts/%s', $merchant->id, $post->id);
        $this->visit($endpoint);
     //        ->attach('/public/images/250x200.png', 'file');

    	// // $response = $this->post($endpoint, [
    	// // 	'file'	=> 'http://google.com/photo.jpg'
    	// // ]);

    	// $this->seeInDatabase('photos', [
    	// 	'imageable_id'	=> $post->id,
    	// 	'imageable_type'	=> 'App\Post',
    	// 	'url'	=> 'http://google.com/photo.jpg'
    	// ]);
    }
}

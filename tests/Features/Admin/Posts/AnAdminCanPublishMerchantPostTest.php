<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanPublishMerchantPostTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin();
	}

    public function test_an_admin_can_publish_a_merchant_post()
    {
    	$merchant = $this->createMerchant();

    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id,
    		'published'	=> false,
    	]);

    	$this->seeInDatabase('posts', [
    		'id'	=> $post->id,
    		'published'	=> 0
    	]);

    	$endpoint = sprintf('%s/dashboard/merchants/%s/posts/%s/toggle', adminPath(), $merchant->id, $post->id);
    	$request = $this->put($endpoint);

    	$this->seeInDatabase('posts', [
    		'id'	=> $post->id,
    		'published'	=> 1
    	]);

	}
}

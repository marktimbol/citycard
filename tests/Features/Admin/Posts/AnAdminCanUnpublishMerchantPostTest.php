<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnAdminCanUnpublishMerchantPostTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsAdmin();
	}

    public function test_an_admin_can_unpublish_a_merchant_post()
    {
    	$merchant = $this->createMerchant();

    	$post = $this->createPost([
    		'merchant_id'	=> $merchant->id,
    		'published'	=> true,
    	]);

    	$this->seeInDatabase('posts', [
    		'id'	=> $post->id,
    		'published'	=> 1
    	]);

    	$endpoint = sprintf('%s/dashboard/merchants/%s/posts/%s/toggle', adminPath(), $merchant->id, $post->id);
    	$request = $this->put($endpoint, [
    		'published'	=> 0,
    	]);

    	$this->seeInDatabase('posts', [
    		'id'	=> $post->id,
    		'published'	=> 0
    	]);

	}
}

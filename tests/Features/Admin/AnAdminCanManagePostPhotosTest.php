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

    public function test_an_admin_can_delete_photo_from_a_post()
    {
    	$this->assertTrue(true);
    }
}

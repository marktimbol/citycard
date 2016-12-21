<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanOrderTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
	}

    public function testExample()
    {
        $this->assertTrue(true);
    }
}

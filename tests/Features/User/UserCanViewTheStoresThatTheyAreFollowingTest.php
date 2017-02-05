<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanViewTheStoresThatTheyAreFollowingTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'Mark Timbol'	
		]);

	}

    public function test_user_can_view_all_the_outlets_that_they_follow()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Starbucks - Al Ghurair Centre'
    	]);

    	$this->user->follows($outlet);
    	
    	$request = $this->json('GET', '/api/user/outlets/following');
    	
		$this->seeJson([
			'name'	=> 'Starbucks - Al Ghurair Centre'
		]);

    }
}

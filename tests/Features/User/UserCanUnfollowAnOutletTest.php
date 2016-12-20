<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCanUnfollowAnOutletTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'John'
		]);
	}

    public function test_a_user_can_unfollow_an_outlet()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Zara'
    	]);

    	$this->user->outlets()->attach($outlet);

    	$this->json('GET', '/api/outlets/1')
    		->seeJson([
    			'is_following'	=> true
    		]);    	

    	$endpoint = sprintf('/api/outlets/%s/unfollow', $outlet->id);
    	$this->delete($endpoint);

    	$this->dontSeeInDatabase('outlet_followers', [
    		'outlet_id'	=> $outlet->id,
    		'user_id'	=> $this->user->id,
    	]);

    	$this->json('GET', '/api/outlets/1')
    		->seeJson([
    			'is_following'	=> false
    		]);
    }
}

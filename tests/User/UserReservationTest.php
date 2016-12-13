<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsUser([
			'name'	=> 'Mark'
		]);
	}

    public function test_an_authenticated_user_can_reserve_a_service_from_an_outlet()
    {
    	$outlet = $this->createOutlet([
    		'name'	=> 'Pizzaro - Dubai Mall'
    	]);

    	$endpoint = sprintf('/api/outlets/%s/reservations', $outlet->id);
    	$request = $this->json('POST', $endpoint, [
    		'api_token'	=> $this->user->api_token,
    		'date'	=> 'The Date',
    		'time'	=> 'The Time',
    		'services'	=> 'Dinner',
    		'note'	=> 'Some notes on the reservations'
    	]);

    	$this->seeInDatabase('reservations', [
    		'user_id'	=> 1,
    		'date'	=> 'The Date',
    		'time'	=> 'The Time',
    		'services'	=> 'Dinner',
    		'note'	=> 'Some notes on the reservations',
    		'confirmed'	=> false
    	])
	    	->seeInDatabase('outlet_reservations', [
	    		'outlet_id'	=> $outlet->id,
	    		'reservation_id'	=> 1,
	    	]);
    }
}

<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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

    public function test_an_authenticated_user_can_reserve_an_item_or_service_from_the_outlet()
    {
        $tomorrow = Carbon::tomorrow()->toDateTimeString();

    	$outlet = $this->createOutlet([
    		'name'	=> 'Burj Khalifa'
    	]);

        $outletItem = $this->createOutletItem([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top'
        ]);

    	$endpoint = sprintf('/api/outlets/%s/reservations', $outlet->id);
    	$request = $this->json('POST', $endpoint, [
            'item_id'  => $outletItem->id,
            'date'  => $tomorrow,
            'time'  => '17:00',
    		'note'	=> 'Some notes on my reservations'
    	]);

    	$this->seeInDatabase('reservations', [
    		'date'	=> $tomorrow,
    		'time'	=> '17:00',
    		'note'	=> 'Some notes on the reservations',
    		'confirmed'	=> false
    	])
	    	->seeInDatabase('outlet_reservations', [
	    		'outlet_id'	=> $outlet->id,
	    		'reservation_id'	=> 1,
	    	])
            ->seeInDatabase('user_reservations', [
                'user_id'   => $this->user->id,
                'reservation_id'    => 1,
            ]);
    }
}

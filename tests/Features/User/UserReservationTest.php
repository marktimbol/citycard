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

    public function test_a_user_can_view_all_his_or_her_reservations()
    {
        $date = Carbon::create(2016, 12, 31, 0)->toDateTimeString();

        $outlet = $this->createOutlet([
            'name'  => 'Dubai Mall',
            'has_reservation'   => true,
        ]);

        $itemForReservation = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top'
        ]);

        $reservation = $this->user->reservations()->create([
            'item_id'  => $itemForReservation->id,
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => 2,
            'note'  => 'The note'
        ]);

        $outlet->reservations()->attach($reservation->id);

        $this->seeInDatabase('reservations', [
            'user_id'   => $this->user->id,
            'item_id'  => $itemForReservation->id,
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => 2,
            'note'  => 'The note',
            'confirmed' => false
        ])
            ->seeInDatabase('outlet_reservations', [
                'outlet_id' => $outlet->id,
                'reservation_id'    => 1,
            ]);

        $request = $this->json('GET', '/api/user/reservations');
        
        $this->seeJson([
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => '2',
            'title' => 'Burj Khalifa - At the Top'
        ]);
    }

    public function test_an_authenticated_user_can_reserve_an_item_or_service_from_the_outlet()
    {
        $date = Carbon::create(2016, 12, 31, 0)->toDateTimeString();

    	$outlet = $this->createOutlet([
    		'name'	=> 'Dubai Mall',
            'has_reservation'   => true,
    	]);

        $itemForReservation = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top'
        ]);

    	$endpoint = sprintf('/api/outlets/%s/reservations', $outlet->id);
    	$request = $this->json('POST', $endpoint, [
            'item_id'  => $itemForReservation->id,
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => 2,
    		'note'	=> 'The note'
    	]);

    	$this->seeInDatabase('reservations', [
            'user_id'   => $this->user->id,
            'item_id'  => $itemForReservation->id,
    		'date'	=> $date,
    		'time'	=> '17:00',
            'quantity'  => 2,
    		'note'	=> 'The note',
    		'confirmed'	=> false
    	])
	    	->seeInDatabase('outlet_reservations', [
	    		'outlet_id'	=> $outlet->id,
	    		'reservation_id'	=> 1,
	    	]);

        $this->seeJson([
            'success'   => true,
        ]);
    }
}

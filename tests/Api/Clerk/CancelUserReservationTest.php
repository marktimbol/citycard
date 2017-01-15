<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CancelUserReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_a_clerk_can_cancel_user_reservation_with_message()
    {
    	$user = $this->createUser([
    		'name'	=> 'Jomerie'
    	]);

        $outlet = $this->createOutlet();

        $item = $outlet->itemsForReservation()->create([
            'title' => 'Burj Khalifa - At the Top'
        ]);

    	$reservationDate = Carbon::tomorrow()->toDateTimeString();
    	$reservation = $this->createReservation([
    		'user_id'	=> $user->id,
    		'item_id'	=> $item->id,
            'date'  => $reservationDate,
            'time'  => '21:00',
            'quantity'  => 2,
            'note'  => 'Reservation note',
            'confirmed' => true,
    	]);

    	$this->seeInDatabase('reservations', [
    		'user_id'	=> $user->id,
    		'date'	=> $reservationDate,
    		'time'	=> '21:00',
    		'quantity'	=> 2,
    		'note'	=> 'Reservation note',
            'confirmed' => true,
    	]);

    	// Cancels the reservation
    	$endpoint = sprintf('/api/clerk/outlets/%s/reservations/%s', $outlet->id, $reservation->id);
    	$request = $this->delete($endpoint, [
    		'message'	=> 'Sorry, your reservation has been cancelled due to some reasons.'
    	]);

        $this->seeInDatabase('reservations', [
            'id'    => $reservation->id,
            'confirmed' => false,
        ])
        	->seeInDatabase('cancelled_reservations', [
        		'outlet_id'	=> $outlet->id,
        		'reservation_id'	=> $reservation->id,
        		'message'	=> 'Sorry, your reservation has been cancelled due to some reasons.'
        	]);
    }
}

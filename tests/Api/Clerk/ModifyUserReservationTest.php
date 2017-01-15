<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModifyUserReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_a_clerk_can_modify_user_reservation()
    {
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $this->clerk->merchant_id
    	]);

    	$item = $outlet->itemsForReservation()->create([
    		'title'	=> 'Burj Khalifa - At the Top'
    	]);

    	$user = $this->createUser([
    		'name'	=> 'Jomerie'
    	]);

    	$reservationDate = Carbon::tomorrow()->toDateTimeString();
    	$reservation = $this->createReservation([
    		'user_id'	=> $user->id,
    		'item_id'	=> $item->id,
    		'date'	=> $reservationDate,
    		'time'	=> '17:00',
    		'flexible_dates'	=> false,
    		'option'	=> 'VVIP',
    		'quantity'	=> 2,
    		'note'	=> 'Reservation note'
    	]);

    	$this->seeInDatabase('reservations', [
    		'user_id'	=> $user->id,
    		'date'	=> $reservationDate,
    		'time'	=> '17:00',
    		'flexible_dates'	=> false,
    		'option'	=> 'VVIP',
    		'quantity'	=> 2,
    		'note'	=> 'Reservation note'
    	]);

    	// Perform reservation modification]
    	$newReservationDate = Carbon::now()->addDays(2)->toDateTimeString();
    	$endpoint = sprintf('/api/clerk/outlets/%s/reservations/%s', $outlet->id, $reservation->id);
    	$request = $this->put($endpoint, [
    		'date'	=> $newReservationDate,
    		'time'	=> '19:00',
    		'option'	=> 'VIP',
    		'note'	=> 'Updated reservation'
    	]);

    	$this->seeInDatabase('reservations', [
    		'id'	=> $reservation->id,
    		'date'	=> $newReservationDate,
    		'time'	=> '19:00',
    		'option'	=> 'VIP',
    		'note'	=> 'Updated reservation',
    		'confirmed'	=> false,
    	]);
    }
}

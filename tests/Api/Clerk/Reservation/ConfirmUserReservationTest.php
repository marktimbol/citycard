<?php

use App\Events\Reservation\ReservationWasConfirmed;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ConfirmUserReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Mark'
		]);
	}

    public function test_a_clerk_can_confirm_a_user_reservation()
    {
    	$this->expectsEvents(ReservationWasConfirmed::class);

        $points = factory(App\Point::class)->create([
            'reservation'   => 500,
        ]);

        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id
        ]);

        $item = $outlet->itemsForReservation()->create([
            'title' => 'Burj Khalifa - At the Top'
        ]);

        // User make a reservation
        $user = $this->createUser([
            'name'  => 'Jomerie'
        ]);

        factory(App\Transaction::class)->create([
            'user_id'   => $user->id,
            'description'   => 'You received 100 points upon registration.',
            'credit'    => 100,
            'debit' => 0,
            'balance'   => 100,
        ]);        

        $reservationDate = Carbon::tomorrow()->toDateTimeString();
        $reservation = $this->createReservation([
        	'user_id'	=> $user->id,
        	'item_id'	=> $item->id,
            'date'  => $reservationDate,
            'time'  => '17:00',
            'flexible_dates'	=> false,
            'option'	=> 'VIP',
            'quantity'  => 2,
            'note'  => 'Reservation note'
        ]);

        // Attach the user's reservation to the outlet
        $outlet->reservations()->attach($reservation);

        // Confirm user's reservation
        $endpoint = sprintf('/api/clerk/outlets/%s/reservations/%s/confirm', $outlet->id, $reservation->id);
        $request = $this->put($endpoint);
        
    	$this->seeInDatabase('reservations', [
    		'id'	=> $reservation->id,
    		'confirmed'	=> true,
            'status'    => 'confirmed'
    	]);

        // Balance will be 600 because initially, the user has 100 points
        // and when the reservation was confirmed, the user will received $points->reservation
        $this->seeInDatabase('transactions', [
            'user_id'   => $user->id,
            'credit'    => $points->reservation,
            'debit' => 0,
            'balance'   => 600,
            'description'   => sprintf('You received %s points because your reservation was approved.', $points->reservation)
        ]);
    }  
}

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

    public function test_a_user_can_view_all_his_or_her_confirmed_reservations()
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

        $itemForReservation2 = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Pending Reservation Item'
        ]);

        $confirmedReservationDate = Carbon::tomorrow()->toDateTimeString();
        $confirmedReservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation->id,
            'date'  => $confirmedReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
            'status'    => 'confirmed'
        ]);

        $pendingReservationDate = Carbon::today()->toDateTimeString();
        $pendingReservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation2->id,
            'date'  => $pendingReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => false,
            'status'    => 'pending'
        ]);

        $outlet->reservations()->attach([$confirmedReservation->id, $pendingReservation->id]);

        $this->seeInDatabase('reservations', [
            'user_id'   => $this->user->id,
            'item_id'  => $itemForReservation->id,
            'date'  => $confirmedReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
            'status'    => 'confirmed'
        ])
            ->seeInDatabase('outlet_reservations', [
                'outlet_id' => $outlet->id,
                'reservation_id'    => 1,
            ]);

        $request = $this->json('GET', '/api/user/reservations');
        
        $this->seeJson([
            'title' => 'Burj Khalifa - At the Top'
        ])
        ->dontSeeJson([
            'title' => 'Pending Reservation Item',
        ]);
    }

    public function test_a_user_can_view_all_his_or_her_pending_reservations()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Dubai Mall',
            'has_reservation'   => true,
        ]);

        $itemForReservation = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top'
        ]);

        $itemForReservation2 = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Pending Reservation Item'
        ]);

        $confirmedReservationDate = Carbon::tomorrow()->toDateTimeString();
        $confirmedReservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation->id,
            'date'  => $confirmedReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ]);

        $pendingReservationDate = Carbon::today()->toDateTimeString();
        $pendingReservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation2->id,
            'date'  => $pendingReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => false,
        ]);

        $outlet->reservations()->attach([$confirmedReservation->id, $pendingReservation->id]);

        $this->seeInDatabase('reservations', [
            'user_id'   => $this->user->id,
            'item_id'  => $itemForReservation->id,
            'date'  => $confirmedReservationDate,
            'time'  => '19:00',
            'flexible_dates'    => true,
            'quantity'  => 1,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ])
            ->seeInDatabase('outlet_reservations', [
                'outlet_id' => $outlet->id,
                'reservation_id'    => 1,
            ]);

        $request = $this->json('GET', '/api/user/reservations?show=pending');
        
        $this->seeJson([
            'title' => 'Pending Reservation Item',
        ])
        ->dontSeeJson([
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
            'title' => 'Burj Khalifa - At the Top',
            'options'   => ['Silver', 'Gold']
        ]);

    	$endpoint = sprintf('/api/outlets/%s/reservations', $outlet->id);
    	$request = $this->json('POST', $endpoint, [
            'item_id'  => $itemForReservation->id,
            'date'  => $date,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'option'   => 'Silver',
            'quantity'  => 2,
    		'note'	=> 'The note'
    	]);
        
    	$this->seeInDatabase('reservations', [
            'user_id'   => $this->user->id,
            'item_id'  => $itemForReservation->id,
    		'date'	=> $date,
    		'time'	=> '17:00',
            'flexible_dates'    => true,
            'option'    => 'Silver',
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

    public function test_an_authenticated_user_can_modify_his_or_her_reservation_from_the_outlet()
    {
        $tomorrow = Carbon::tomorrow()->toDateTimeString();

        $outlet = $this->createOutlet([
            'name'  => 'Dubai Mall',
            'has_reservation'   => true,
        ]);

        $itemForReservation = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top',
            'options'   => ['Silver', 'Gold']
        ]);

        $reservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation->id,
            'date'  => $tomorrow,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'option'    => 'Silver',
            'quantity'  => 2,
            'note'  => 'The note',
            'confirmed' => true
        ]);

        $outlet->reservations()->attach($reservation->id);

        // Perform reservation modification

        $dayAfterTomorrow = Carbon::now()->addDays(2)->toDateTimeString();
        $endpoint = sprintf('/api/outlets/%s/reservations/%s', $outlet->id, $reservation->id);
        $request = $this->put($endpoint, [
            'date'  => $dayAfterTomorrow,
            'time'  => '9:00pm',
            'flexible_dates'    => false,
            'option'    => 'Gold',
            'quantity'  => 1,
            'note'  => 'Updated',
        ]);

        $this->seeInDatabase('reservations', [
            'id'    => $reservation->id,
            'date'  => $dayAfterTomorrow,
            'time'  => '9:00pm',
            'flexible_dates'    => false,
            'option'    => 'Gold',
            'quantity'  => 1,
            'note'  => 'Updated',
            'confirmed' => false
        ]);
    }

    public function test_an_authenticated_user_can_cancel_his_or_her_reservation_from_the_outlet()
    {
        $date = Carbon::tomorrow()->toDateTimeString();

        $outlet = $this->createOutlet([
            'name'  => 'Dubai Mall',
            'has_reservation'   => true,
        ]);

        $itemForReservation = $this->createItemForReservation([
            'outlet_id' => $outlet->id,
            'title' => 'Burj Khalifa - At the Top',
            'options'   => ['Silver', 'Gold']
        ]);

        $reservation = $this->createReservation([
            'user_id'   => $this->user->id,
            'item_id'   => $itemForReservation->id,
            'date'  => $date,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'option'    => 'Silver',
            'quantity'  => 2,
            'note'  => 'The note',
            'confirmed' => true
        ]);

        $outlet->reservations()->attach($reservation->id);

        // Perform cancellation
        $endpoint = sprintf('/api/outlets/%s/reservations/%s', $outlet->id, $reservation->id);
        $request = $this->delete($endpoint);

        $this->seeInDatabase('reservations', [
            'id'    => $reservation->id,
            'confirmed' => false,
        ])
            ->seeInDatabase('cancelled_reservations', [
                'outlet_id' => $outlet->id,
                'reservation_id'    => $reservation->id,
            ]);
    }
}

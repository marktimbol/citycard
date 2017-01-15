<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OutletReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'John'
		]);
	}

    public function test_a_clerk_can_view_all_the_outlet_confirmed_reservations()
    {
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $this->clerk->merchant_id
    	]);

    	$item = $outlet->itemsForReservation()->create([
    		'title'	=> 'Burj Khalifa - At the Top'
    	]);

        $outlet->itemsForReservation()->create([
            'title' => 'Pending'
        ]);        

    	$user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

    	$reservationDate = Carbon::today()->toDateTimeString();
        $confirmedReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => $item->id,
            'date'  => $reservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ]);

        $pendingReservationDate = Carbon::tomorrow()->toDateTimeString();
        $pendingReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => 2,
            'date'  => $pendingReservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => false,
        ]);        

    	// Attach the user reservation on the outlet
    	$outlet->reservations()->attach([$confirmedReservation->id, $pendingReservation->id]);

    	$request = $this->get('/api/clerk/outlets/'.$outlet->id.'/reservations');
    	$this->seeJson([
            'name'  => 'Mark Timbol',
    		'title'	=> 'Burj Khalifa - At the Top',
    		'date'	=> $reservationDate,
    		'time'	=> '17:00',
    		'quantity'	=> 2,
    	])
            ->dontSeeJson([
                'title' => 'Pending'
            ]);
    }

    public function test_a_clerk_can_view_all_the_outlet_pending_reservations()
    {
        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id
        ]);

        $item = $outlet->itemsForReservation()->create([
            'title' => 'Burj Khalifa - At the Top'
        ]);

        $outlet->itemsForReservation()->create([
            'title' => 'Pending'
        ]);        

        $user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

        $reservationDate = Carbon::today()->toDateTimeString();
        $confirmedReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => $item->id,
            'date'  => $reservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ]);

        $pendingReservationDate = Carbon::tomorrow()->toDateTimeString();
        $pendingReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => 2,
            'date'  => $pendingReservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => false,
        ]);        

        // Attach the user reservation on the outlet
        $outlet->reservations()->attach([$confirmedReservation->id, $pendingReservation->id]);

        $request = $this->get('/api/clerk/outlets/'.$outlet->id.'/reservations?show=pending');
        $this->dontSeeJson([
            'title' => 'Burj Khalifa - At the Top',
        ])
            ->seeJson([
                'title' => 'Pending'
            ]);
    } 

    public function test_a_clerk_can_view_all_the_outlet_cancelled_reservations()
    {
        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id
        ]);

        $outlet->itemsForReservation()->create([
            'title' => 'Confirmed Reservation'
        ]);

        $outlet->itemsForReservation()->create([
            'title' => 'Cancelled Reservation'
        ]);        

        $user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

        $reservationDate = Carbon::today()->toDateTimeString();
        $confirmedReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => 1,
            'date'  => $reservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ]);  

        $cancelledReservationDate = Carbon::tomorrow()->toDateTimeString();
        $cancelledReservation = $this->createReservation([
            'user_id'   => $user->id,
            'item_id'   => 2,
            'date'  => $cancelledReservationDate,
            'time'  => '17:00',
            'flexible_dates'    => true,
            'quantity'  => 2,
            'option'    => 'VIP',
            'note'  => 'Reservation note',
            'confirmed' => true,
        ]);  

        // Attach the user reservation on the outlet
        $outlet->reservations()->attach([$confirmedReservation->id, $cancelledReservation->id]);
        
        // Attach the cancelled reservation on the outlet
        $outlet->cancelledReservations()->attach($cancelledReservation->id);

        $request = $this->get('/api/clerk/outlets/'.$outlet->id.'/reservations?show=cancelled');
        $this->dontSeeJson([
            'title' => 'Confirmed Reservation',
        ])
        ->seeJson([
            'title' => 'Cancelled Reservation'
        ]);
    }       

    public function test_a_clerk_can_view_an_outlet_reservation()
    {
        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id
        ]);

        $item = $outlet->itemsForReservation()->create([
            'title' => 'Burj Khalifa - At the Top'
        ]);

        // User make a reservation
        $user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

        $date = Carbon::tomorrow();
        $reservation =$user->reservations()->create([
            'item_id'   => $item->id,
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => 2,
            'note'  => 'The note'       
        ]);
        // Attach the user reservation on the outlet
        $outlet->reservations()->attach($reservation);

        $request = $this->get('/api/clerk/outlets/'.$outlet->id.'/reservations/1');
        $this->seeJson([
            'name'  => 'Mark Timbol',
            'title' => 'Burj Khalifa - At the Top',
            'date'  => $date->toDateTimeString(),
            'time'  => '17:00',
            'quantity'  => 2,
        ]);
    }         
}

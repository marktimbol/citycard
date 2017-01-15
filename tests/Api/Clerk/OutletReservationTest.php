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

    public function test_a_clerk_can_view_all_the_outlet_reservations()
    {
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $this->clerk->merchant_id
    	]);

    	$item = $outlet->itemsForReservation()->create([
    		'title'	=> 'Burj Khalifa - At the Top'
    	]);

    	// User make a reservation
    	$user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

    	$date = Carbon::tomorrow();
    	$reservation =$user->reservations()->create([
    		'item_id'	=> $item->id,
            'date'  => $date,
            'time'  => '17:00',
            'quantity'  => 2,
            'note'  => 'The note'    	
    	]);
    	// Attach the user reservation on the outlet
    	$outlet->reservations()->attach($reservation);

    	$request = $this->get('/api/clerk/outlets/'.$outlet->id.'/reservations');
    	$this->seeJson([
            'name'  => 'Mark Timbol',
    		'title'	=> 'Burj Khalifa - At the Top',
    		'date'	=> $date->toDateTimeString(),
    		'time'	=> '17:00',
    		'quantity'	=> 2,
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

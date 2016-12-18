<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CanFilterEventsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_guest_user_can_filter_events_by_from_and_to_date()
    {
		$decemberTwo = Carbon::create(2016, 12, 2, 0);
		$decemberTwo = $decemberTwo->toDateTimeString(); 

		$decemberThree = Carbon::create(2016, 12, 3, 0);
		$decemberThree = $decemberThree->toDateTimeString();

 		$decemberFifteen = Carbon::create(2016, 12, 15, 0);
		$decemberFifteen = $decemberFifteen->toDateTimeString();

    	$this->createPost([
    		'title'	=> 'Event on December 2',
    		'type'	=> 'events',
    		'event_date'	=> $decemberTwo
    	]);

    	$this->createPost([
    		'title'	=> 'Event on December 3',
    		'type'	=> 'events',
    		'event_date'	=> $decemberThree,
    	]);    	

    	$this->createPost([
    		'title'	=> 'Event on December 15',
    		'type'	=> 'events',
    		'event_date'	=> $decemberFifteen
    	]);    	

    	$endpoint = sprintf('/api/events/?filter=true&from=%s&to=%s', '2016-12-01', '2016-12-10');

    	$this->json('GET', $endpoint)
    		->seeJson([
    			'title'	=> 'Event on December 2'
    		])
    		->seeJson([
    			'title'	=> 'Event on December 3'
    		])
    		->dontSeeJson([
    			'title'	=> 'Event on December 15'
    		]);
    }

    public function test_a_guest_user_can_view_all_the_events_on_a_specific_date()
    {
        $decemberFifteen = Carbon::create(2016, 12, 15, 0);
        $decemberFifteen = $decemberFifteen->toDateTimeString();

        // dd($decemberFifteen);

        $decemberThirty = Carbon::create(2016, 12, 30, 0);
        $decemberThirty = $decemberThirty->toDateTimeString();        

        $this->createPost([
            'title' => 'Event on December 15',
            'type'  => 'events',
            'event_date'    => $decemberFifteen
        ]);

        $this->createPost([
            'title' => 'Another Event on December 15',
            'type'  => 'events',
            'event_date'    => $decemberFifteen,
        ]);     

        $this->createPost([
            'title' => 'Event on December 30',
            'type'  => 'events',
            'event_date'    => $decemberThirty
        ]);     

        $endpoint = sprintf('/api/events/?filter=true&from=%s&to=%s', '2016-12-15', '2016-12-15');

        $this->json('GET', $endpoint)
            ->seeJson([
                'title' => 'Event on December 15'
            ])
            ->seeJson([
                'title' => 'Another Event on December 15'
            ])
            ->dontSeeJson([
                'title' => 'Event on December 30'
            ]);
    }    
}

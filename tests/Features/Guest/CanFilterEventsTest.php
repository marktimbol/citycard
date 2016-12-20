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
        $yesterday = Carbon::yesterday();
        $today = Carbon::now()->subHour();
        $tomorrow = Carbon::tomorrow();
        $nextYear = Carbon::now()->addYear(1);

    	$this->createPost([
    		'title'	=> 'Event yesterday',
    		'type'	=> 'events',
    		'event_date'	=> $yesterday->toDateTimeString()
    	]);

    	$post = $this->createPost([
    		'title'	=> 'Event today',
    		'type'	=> 'events',
    		'event_date'	=> $today->toDateTimeString()
    	]);    	

    	$this->createPost([
    		'title'	=> 'Event tomorrow',
    		'type'	=> 'events',
    		'event_date'	=> $tomorrow->toDateTimeString()
    	]); 

        $this->createPost([
            'title' => 'Event next year',
            'type'  => 'events',
            'event_date'    => $nextYear->toDateTimeString()
        ]);            	

    	$endpoint = sprintf('/api/events/?filter=1&from=%s&to=%s', $today->toDateString(), $tomorrow->toDateString());

    	$this->json('GET', $endpoint)
    		->seeJson([
    			'title'	=> 'Event today'
    		])
    		->seeJson([
    			'title'	=> 'Event tomorrow'
    		])
    		->dontSeeJson([
    			'title'	=> 'Event yesterday'
    		])
            ->dontSeeJson([
                'title' => 'Event next year'
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

<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CanFilterEventsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_a_guest_user_can_filter_events()
    {
		$decemberTwo = Carbon::create(2016, 12, 2, 0);
		$decemberTwo = $decemberTwo->toDateTimeString(); 

		$decemberThree = Carbon::create(2016, 12, 3, 0);
		$decemberThree = $decemberThree->toDateTimeString();

 		$decemberFifteen = Carbon::create(2016, 12, 15, 0);
		$decemberFifteen = $decemberFifteen->toDateTimeString();

		// dd($decemberTwo, $decemberThree, $decemberFifteen);

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
}

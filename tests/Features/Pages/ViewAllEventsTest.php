<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewAllEventsTest extends TestCase
{
	use DatabaseMigrations;

    public function test_view_all_upcoming_events()
    {
        $previousEventDate = Carbon::now()->subYear()->toDateTimeString();        
        $eventDate = Carbon::tomorrow()->toDateTimeString();

        $deals = $this->createPost([
            'type'  => 'deals',
            'title' => 'Not an event',
            'published' => true,
        ]);

        $previousEvent = $this->createPost([
            'title' => 'Hillsong Concert last year',
            'type'    => 'events',
            'event_date'    => $previousEventDate,
            'event_time'    => '17:00 - 23:00',
            'published' => true,
        ]);

    	$event = $this->createPost([
    		'title'	=> 'Hillsong Concert tomorrow',
    		'type'    => 'events',
            'event_date'    => $eventDate,
            'event_time'    => '17:00 - 23:00',
            'published' => true,
    	]);

    	$this->visit('/events')
    		->see('Hillsong Concert tomorrow')
            ->dontSee('Hillsong Concert last year')
            ->dontSee('Not an event');
    }
}

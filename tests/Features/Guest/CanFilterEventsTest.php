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
        $today = Carbon::now()->addHour();
        $tomorrow = Carbon::tomorrow();
        $nextYear = Carbon::now()->addYear(1);

        $this->createPost([
            'title' => 'Event yesterday',
            'type'  => 'events',
            'event_date'    => $yesterday->toDateTimeString()
        ]);

        $post = $this->createPost([
            'title' => 'Event today',
            'type'  => 'events',
            'event_date'    => $today->toDateTimeString()
        ]);     

        $this->createPost([
            'title' => 'Event tomorrow',
            'type'  => 'events',
            'event_date'    => $tomorrow->toDateTimeString()
        ]); 

        $this->createPost([
            'title' => 'Event next year',
            'type'  => 'events',
            'event_date'    => $nextYear->toDateTimeString()
        ]);             

        $endpoint = sprintf('/api/events/?filter=1&from=%s&to=%s', $today->toDateString(), $tomorrow->toDateString());

        $this->json('GET', $endpoint)
            ->seeJson([
                'title' => 'Event today'
            ])
            ->seeJson([
                'title' => 'Event tomorrow'
            ])
            ->dontSeeJson([
                'title' => 'Event yesterday'
            ])
            ->dontSeeJson([
                'title' => 'Event next year'
            ]);
    }

    public function test_a_guest_user_can_view_all_the_events_on_a_given_date()
    {
        $today = Carbon::now()->addHour();
        $tomorrow = Carbon::tomorrow();

        $post = $this->createPost([
            'title' => 'Event today',
            'type'  => 'events',
            'event_date'    => $today->toDateTimeString()
        ]); 

        $this->createPost([
            'title' => 'Event tomorrow',
            'type'  => 'events',
            'event_date'    => $tomorrow->toDateTimeString()
        ]);           

        $endpoint = 
            sprintf('/api/events/?filter=1&from=%s&to=%s', $today->toDateString(), $today->toDateString());

        $this->json('GET', $endpoint)
            ->seeJson([
                'title' => 'Event today'
            ])
            ->dontSeeJson([
                'title' => 'Event tomorrow'
            ]);
    }    
}
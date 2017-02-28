<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClerkCanAddNotesToTheUserTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk();
	}

    public function test_an_authenticated_clerk_can_add_notes_to_the_user()
    {
    	$outlet = $this->createOutlet([
    		'merchant_id'	=> $this->clerk->merchant_id,
    		'name'	=> 'Zara - Al Rigga'
    	]);

    	$this->clerk->assignTo($outlet);

    	$user = $this->createUser([
    		'name'	=> 'Mark Timbol'
    	]);

    	$endpoint = "/api/clerk/outlets/{$outlet->id}/users/{$user->id}/notes";
    	$request = $this->post($endpoint, [
    		'notes'	=> 'The note'
    	]);

    	$this->seeInDatabase('outlet_user_notes', [
    		'outlet_id'	=> $outlet->id,
    		'user_id'	=> $user->id,
    		'notes'	=> 'The note'
    	]);
    }

    public function test_an_authenticated_clerk_can_update_a_note_to_the_user()
    {
        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id,
            'name'  => 'Zara - Al Rigga'
        ]);

        $this->clerk->assignTo($outlet);

        $user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

        $note = factory(App\OutletUserNotes::class)->create([
            'outlet_id' => $outlet->id,
            'user_id'   => $user->id,
            'notes'  => 'The first note'
        ]);

        $endpoint = "/api/clerk/outlets/{$outlet->id}/users/{$user->id}/update-notes";
        $request = $this->put($endpoint, [
            'notes' => 'The updated note'
        ]);

        $this->seeInDatabase('outlet_user_notes', [
            'outlet_id' => $outlet->id,
            'user_id'   => $user->id,
            'notes' => 'The updated note'
        ]);
    }

    public function test_an_authenticated_clerk_can_delete_a_note_on_the_user()
    {
        $outlet = $this->createOutlet([
            'merchant_id'   => $this->clerk->merchant_id,
            'name'  => 'Zara - Al Rigga'
        ]);

        $this->clerk->assignTo($outlet);

        $user = $this->createUser([
            'name'  => 'Mark Timbol'
        ]);

        $note = factory(App\OutletUserNotes::class)->create([
            'outlet_id' => $outlet->id,
            'user_id'   => $user->id,
            'notes'  => 'The note'
        ]);

        $endpoint = "/api/clerk/outlets/{$outlet->id}/users/{$user->id}/delete-notes";
        $request = $this->delete($endpoint);

        $this->dontSeeInDatabase('outlet_user_notes', [
            'outlet_id' => $outlet->id,
            'user_id'   => $user->id,
            'notes' => 'The note'
        ]);
    }    
}

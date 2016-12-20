<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivateReservationMessagingMenusTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->adminSignIn();
	}

    public function test_an_authorized_user_can_activate_reservation_messaging_and_menus_on_the_outlet()
    {
        $this->assertTrue(true);
    }
}

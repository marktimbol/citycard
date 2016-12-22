<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageItemsForReservationTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->adminSignIn();
	}

	public function test_view_all_the_items_for_reservation_of_an_outlet()
	{
		$merchant = $this->createMerchant([
			'name'	=> 'Dubai Mall',
		]);

		$outlet = $this->createOutlet([
			'merchant_id'	=> $merchant->id,
			'name'	=> 'Burj Khalifa'
		]);

		$itemForReservation = $this->createItemForReservation([
			'title'	=> 'Burj Khalifa - At the Top'
		]);

		$outlet->itemsForReservation()->save($itemForReservation);

		$url = sprintf('%s/dashboard/merchants/%s/outlets/%s', adminPath(), $merchant->id, $outlet->id);
		$this->visit($url)
			->see('Burj Khalifa - At the Top');
	}

    public function test_an_authorized_user_can_add_items_for_reservation_to_outlet()
    {
		$outlet = $this->createOutlet([
			'name'	=> 'Burj Khalifa'
		]);

		$endpoint = sprintf('%s/dashboard/outlets/%s/for-reservations', adminPath(), $outlet->id);

		$request = $this->post($endpoint, [
			'title'	=> 'Burj Khalifa - At the Top'
		]);

		$this->seeInDatabase('item_for_reservations', [
			'outlet_id'	=> $outlet->id,
			'title'	=> 'Burj Khalifa - At the Top'
		]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageAreasTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_view_all_the_available_areas_of_the_selected_city()
    {
    	$country = $this->createCountry();
    	$city = $this->createCity([
    		'country_id'	=> $country->id
    	]);
    	$area = $this->createArea([
    		'city_id'	=> $city->id
    	]);

    	$this->visit(sprintf(adminPath() . '/dashboard/cities/%s/areas', $city->id))
    		->see($area->name);
    }
}

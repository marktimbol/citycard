<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCitiesTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_view_all_the_available_cities_of_the_selected_country()
    {
    	$country = $this->createCountry();
    	$city = $this->createCity([
    		'country_id'	=> $country->id
    	]);

    	$this->visit(sprintf(adminPath() . '/dashboard/countries/%s/cities', $country->id))
    		->see($city->name);
    }

    public function test_an_admin_can_add_a_city_to_the_country()
    {
        $country = $this->createCountry();
        
        $url = sprintf(adminPath() . '/dashboard/countries/%s/cities', $country->id);
        $this->visit($url)
            ->type('Dubai', 'name')
            ->press('Save')

            ->seeInDatabase('cities', [
                'country_id'    => $country->id,
                'name'  => 'Dubai',
                'slug'  => 'dubai'
            ]);
    }
}

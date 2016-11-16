<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCountriesTest extends TestCase
{
	use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_view_all_the_available_countries_alphabetically()
    {
    	$country = $this->createCountry();
    	$this->visit('/dashboard/countries')
    		->see($country->name);
    }

    public function test_an_admin_can_add_a_new_country()
    {
    	$this->visit('/dashboard/countries')
    		->type('United Arab Emirates', 'name')
    		->press('Save')

    		->seeInDatabase('countries', [
    			'name'	=> 'United Arab Emirates',
    			'slug'	=> 'united-arab-emirates'
    		]);
    }
}

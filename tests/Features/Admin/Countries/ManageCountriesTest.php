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
    	$country = $this->createCountry([
            'name'  => 'United Arab Emirates',
            'iso_code'  => 'AE'
        ]);
    	$this->visit(adminPath() . '/dashboard/countries')
            ->see('AE')
    		->see('United Arab Emirates');
    }

    public function test_an_admin_can_add_a_new_country()
    {
    	$this->visit(adminPath() . '/dashboard/countries')
            ->type('AE', 'iso_code')
            ->type('United Arab Emirates', 'name')
    		->press('Save')

    		->seeInDatabase('countries', [
                'iso_code'  => 'AE',
    			'name'	=> 'United Arab Emirates',
    			'slug'	=> 'united-arab-emirates'
    		]);
    }

    public function test_an_admin_can_update_country_information()
    {
        $country = $this->createCountry([
            'name'  => 'United Arab Emirates',
            'iso_code'  => 'UAE'
        ]);

        $this->visit(adminPath() . '/dashboard/countries/'.$country->id.'/edit')
            ->type('AE', 'iso_code')
            ->press('Update')

            ->seeInDatabase('countries', [
                'id'    => $country->id,
                'iso_code'  => 'AE'
            ]);
    }
}

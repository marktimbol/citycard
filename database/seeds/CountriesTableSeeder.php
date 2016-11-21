<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = factory(App\Country::class)->create([
            'name'  => 'United Arab Emirates',
            'slug'  => 'united-arab-emirates'
        ]);

        $city = factory(App\City::class)->create([
            'country_id'    => $country->id,
            'name'  => 'Dubai',
            'slug'  => 'dubai',
        ]);

        factory(App\Area::class)->create([
            'city_id'    => $city->id,
            'name'  => 'Dubai Internet City',
            'slug'  => 'dubai-internet-city',
        ]);

        factory(App\Area::class)->create([
            'city_id'    => $city->id,
            'name'  => 'Al Barsha',
            'slug'  => 'al-barsha',
        ]);
    }
}

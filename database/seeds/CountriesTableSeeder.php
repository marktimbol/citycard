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
            'iso_code'   => 'AE',
            'name'  => 'United Arab Emirates',
            'slug'  => 'united-arab-emirates'
        ]);

        factory(App\City::class)->create([
            'country_id'    => $country->id,
            'name'  => 'Dubai',
            'slug'  => 'dubai',
        ]);

        factory(App\City::class)->create([
            'country_id'    => $country->id,
            'name'  => 'Abu Dhabi',
            'slug'  => 'abu-dhabi',
        ]);

        factory(App\City::class)->create([
            'country_id'    => $country->id,
            'name'  => 'Sharjah',
            'slug'  => 'sharjah',
        ]);

        // factory(App\Area::class)->create([
        //     'city_id'    => $city->id,
        //     'name'  => 'Dubai Internet City',
        //     'slug'  => 'dubai-internet-city',
        // ]);
        //
        // factory(App\Area::class)->create([
        //     'city_id'    => $city->id,
        //     'name'  => 'Al Barsha',
        //     'slug'  => 'al-barsha',
        // ]);
        //
        // // Abu Dhabi
        //
        // $city = factory(App\City::class)->create([
        //     'country_id'    => $country->id,
        //     'name'  => 'Abu Dhabi',
        //     'slug'  => 'abu-dhabi',
        // ]);
        //
        // factory(App\Area::class)->create([
        //     'city_id'    => $city->id,
        //     'name'  => 'Al Nahyan',
        //     'slug'  => 'al-nahyan',
        // ]);
        //
        // factory(App\Area::class)->create([
        //     'city_id'    => $city->id,
        //     'name'  => 'Al Mushrif',
        //     'slug'  => 'al-mushrif',
        // ]);
    }
}

<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'uuid' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'mobile' => '+971 56 820 7189',
        'api_token' => str_random(60),
        'mobile_verified' => true,
        'email_verified' => true,
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Admin::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Merchant::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => ucfirst($faker->word),
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'currency' => 'AED',
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Outlet::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'name' => ucfirst($faker->word),
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'currency' => 'AED',
        'address'  => $faker->address,
        'lat'  => $faker->latitude,
        'lng'  => $faker->longitude,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
        'has_reservation' => false,
    ];
});

$factory->define(App\Reservation::class, function (Faker\Generator $faker) {
    return [
        'user_id'   => function() {
            return factory(App\User::class)->create()->id;
        },
        'item_id'   => function() {
            return factory(App\ItemForReservation::class)->create()->id;
        },        
        'date'  => \Carbon\Carbon::tomorrow()->toDateTimeString(),
        'time'  => '17:00',
        'flexible_dates'    => true,
        'option'    => 'Silver',
        'quantity'  => 2,
        'note'  => 'The note',
        'confirmed' => false,
        'status'    => 'pending',
    ];
});

$factory->define(App\ItemForReservation::class, function (Faker\Generator $faker) {
    return [
        'outlet_id'   => function() {
            return factory(App\Outlet::class)->create()->id;
        },
        'title' => $faker->sentence,
        'options'    => [],
    ];
});

$factory->define(App\Clerk::class, function (Faker\Generator $faker) {    
    static $password;

    return [
        'uuid' => $faker->uuid,
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'is_online' => false,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
        'last_logged_in' => Carbon::now(),
    ];
});

$factory->define(App\Post::class, function (Faker\Generator $faker) {
    return [
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'category_id'   => function() {
            return factory(App\Category::class)->create()->id;
        },
        'type'  => 'notification',
        'event_date'  => Carbon::now(),
        'event_time'  => '',
        'title' => $faker->sentence,
        'slug'  => $faker->slug,
        'desc'   => $faker->paragraph,
        'isExternal'   => 0,
        'published'  => 1,
    ];
});

$factory->define(App\Reward::class, function (Faker\Generator $faker) {
    return [
        'id'    => $faker->uuid,
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'title' => $faker->sentence,
        'slug'  => $faker->slug,
        'quantity'  => $faker->randomNumber(2),
        'required_points'   => $faker->randomNumber(2),
        'desc'   => $faker->paragraph,
    ];
});

$factory->define(App\Voucher::class, function (Faker\Generator $faker) {
    return [
        'id'    => $faker->uuid,
        'reward_id'   => function() {
            return factory(App\Reward::class)->create()->id;
        },
        'verification_code' => str_random(7),
        'redeemed'  => false,
    ];
});

$factory->define(App\Promo::class, function (Faker\Generator $faker) {
    return [
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'title' => $faker->sentence
    ];
});

$factory->define(App\Country::class, function (Faker\Generator $faker) {
    $country = $faker->country;
    $slug = str_slug($country);

    return [
        'name' => $country,
        'slug'  => $slug
    ];
});

$factory->define(App\City::class, function (Faker\Generator $faker) {
    $city = $faker->city;
    $slug = str_slug($city);

    return [
        'country_id'    => function() {
            return factory(App\Country::class)->create()->id;
        },
        'name' => $city,
        'slug'  => $slug
    ];
});

$factory->define(App\Area::class, function (Faker\Generator $faker) {
    $area = $faker->state;
    $slug = str_slug($area);

    return [
        'city_id'    => function() {
            return factory(App\City::class)->create()->id;
        },
        'name' => $area,
        'slug'  => $slug
    ];
});

$factory->define(App\Source::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Subcategory::class, function (Faker\Generator $faker) {
    return [
        'category_id'   => function() {
            return factory(App\Category::class)->create()->id;
        },
        'name' => $faker->word,
    ];
});

$factory->define(App\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'label' => $faker->word,
    ];
});

$factory->define(App\Album::class, function (Faker\Generator $faker) {
    return [
        'outlet_id' => function() {
            return factory(App\Outlet::class)->create()->id;
        },
        'title' => $faker->word,
    ];
});

$factory->define(App\AlbumPhoto::class, function (Faker\Generator $faker) {
    return [
        'album_id' => function() {
            return factory(App\Album::class)->create()->id;
        },
        'url' => $faker->url,
    ];
});

$factory->define(App\OutletMenu::class, function (Faker\Generator $faker) {
    return [
        'outlet_id' => function() {
            return factory(App\Outlet::class)->create()->id;
        },
        'url' => $faker->url,
    ];
});

$factory->define(App\ShopFront::class, function (Faker\Generator $faker) {
    return [
        'outlet_id' => function() {
            return factory(App\Outlet::class)->create()->id;
        },
        'url' => $faker->url,
    ];
});

$factory->define(App\OutletUserNotes::class, function (Faker\Generator $faker) {
    return [
        'outlet_id' => function() {
            return factory(App\Outlet::class)->create()->id;
        },
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },        
        'notes' => $faker->paragraph,
    ];
});

$factory->define(App\Point::class, function (Faker\Generator $faker) {
    return [
        'registration' => 100,
    ];
});

$factory->define(App\Transaction::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'description'   => 'You received 100 points upon registration.',
        'credit'    => 0,
        'debit' => 0,
        'balance'   => 0, 
    ];
});



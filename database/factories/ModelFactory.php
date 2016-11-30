<?php

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
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'api_token' => str_random(60),
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
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'address1'  => $faker->address,
        'address2'  => $faker->address,
        'latitude'  => $faker->latitude,
        'longitude'  => $faker->longitude,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Clerk::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
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
        'title' => $faker->sentence,
        'slug'  => $faker->slug,
        'desc'   => $faker->paragraph,
        'isExternal'   => 0,
        'approved'  => 0,
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
        'name' => $faker->word,
    ];
});

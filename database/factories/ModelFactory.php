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
        'country'   => $faker->country,
        'city'  => $faker->city,
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
        'type'  => 'Branch',
        'country'   => $faker->country,
        'city'  => $faker->city,
        'area'  => $faker->city,
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
        'country'   => $faker->country,
        'city'  => $faker->city,
        'api_token' => str_random(60),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Post::class, function (Faker\Generator $faker) {
    return [
        'merchant_id'   => function() {
            return factory(App\Merchant::class)->create()->id;
        },
        'type'  => 'notification',
        'title' => $faker->sentence,
        'slug'  => $faker->slug,
        'price'  => $faker->randomNumber(2),
        'desc'   => $faker->paragraph,
        'link'   => $faker->url,
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

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

$factory->define(\App\Address::class, function (Faker\Generator $faker) {
    return [
        'territory_id' => $faker->randomDigitNotNull,
        'street_id' => $faker->randomDigitNotNull,
        'inactive' => 0,
        'lat' => $faker->latitude,
        'long' => $faker->longitude,
        'name' => $faker->name,
        'address' => rand(100, 50000),
        'phone' => $faker->phoneNumber,
        'apt' => '',
    ];
});

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

$factory->define(\App\Note::class, function (Faker\Generator $faker) {
    return [
        'archived' => 0,
        'entity_id' => null,
        'entity' => 'Address',
        'date' => date('Y-m-d'),
        'content' => 'A',
    ];
});

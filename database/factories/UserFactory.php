<?php

use KRLX\Term;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(KRLX\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});

$factory->state(KRLX\User::class, 'carleton_new', function ($faker) {
    return [
        'email' => $faker->username.'@carleton.edu',
        'year' => date('Y') + $faker->numberBetween(1, 3),
    ];
});

$factory->state(KRLX\User::class, 'carleton', function ($faker) {
    return [
        'email' => $faker->username.'@carleton.edu',
        'year' => date('Y') + $faker->numberBetween(1, 3),
        'phone_number' => $faker->regexify('507222[0-9]{4}'),
    ];
});

$factory->state(KRLX\User::class, 'contract_ok', []);

$factory->state(KRLX\User::class, 'board', []);

$factory->afterCreatingState(KRLX\User::class, 'contract_ok', function ($user, $faker) {
    foreach (Term::all() as $term) {
        $user->points()->create(['term_id' => $term->id, 'status' => 'provisioned']);
    }
});

$factory->afterCreatingState(KRLX\User::class, 'board', function ($user, $faker) {
    $user->assignRole('board');
});

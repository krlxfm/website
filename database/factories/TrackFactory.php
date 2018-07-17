<?php

use Faker\Generator as Faker;

$factory->define(KRLX\Track::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->words(2, true)),
        'description' => $faker->paragraph,
    ];
});

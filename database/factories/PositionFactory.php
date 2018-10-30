<?php

use Faker\Generator as Faker;

$factory->define(KRLX\Position::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => '<p>'.$faker->paragraph.'</p>',
        'abbr' => $faker->regexify('[A-Z]{2-4}'),
    ];
});

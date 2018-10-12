<?php

use Faker\Generator as Faker;

$factory->define(KRLX\Track::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->words(2, true)),
        'description' => $faker->paragraph,
        'active' => true,
    ];
});

$factory->state(KRLX\Track::class, 'non_weekly', function ($faker) {
    return [
        'weekly' => false,
        'awards_xp' => false,
        'start_day' => $faker->date('l'),
        'start_time' => $faker->time('H').':00',
        'end_time' => $faker->time('H').':00',
    ];
});

$factory->state(KRLX\Track::class, 'custom_field', [
    'content' => [
        [
            'title' => 'Sponsor',
            'type' => 'shorttext',
            'db' => 'sponsor',
            'helptext' => null,
            'rules' => ['required', 'min:3']
        ]
    ]
]);

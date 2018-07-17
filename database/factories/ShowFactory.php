<?php

use Faker\Generator as Faker;

$factory->define(KRLX\Show::class, function (Faker $faker) {
    return [
        'title' => $faker->catchPhrase,
        'source' => 'factory',
        'term_id' => function () {
            return factory(KRLX\Term::class)->create([
                'accepting_applications' => true,
            ])->id;
        },
        'track_id' => function () {
            return factory(KRLX\Track::class)->create([
                'active' => true,
            ])->id;
        },
    ];
});

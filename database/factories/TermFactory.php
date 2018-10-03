<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(KRLX\Term::class, function (Faker $faker) {
    $date = $faker->dateTimeThisYear();
    $futureDate = Carbon::instance($date);
    $closeDate = $futureDate->copy();

    return [
        'id' => date('Y').'-'.$faker->regexify('[A-Z0-9][A-Z0-9_]+[A-Z0-9]'),
        'applications_close' => $closeDate->subDay(),
        'on_air' => $date,
        'off_air' => $futureDate->addWeeks(2),
        'boosted' => true
    ];
});

$factory->state(KRLX\Term::class, 'active', [
    'status' => 'active'
]);

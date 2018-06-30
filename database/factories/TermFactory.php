<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(KRLX\Term::class, function (Faker $faker) {
    $date = $faker->dateTimeThisYear();
    $futureDate = Carbon::instance($date);
    return [
        'id' => date('Y').'-'.$faker->regexify('[A-Z][A-Z_]+[A-Z]'),
        'on_air' => $date,
        'off_air' => $futureDate->addWeeks(2)
    ];
});

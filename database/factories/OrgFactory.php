<?php

use Faker\Generator as Faker;

$factory->define(App\Org::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});

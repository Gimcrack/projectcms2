<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->sentence,
    ];
});

$factory->state(App\Project::class, 'approved', function (Faker $faker) {
    $approver = create_state(User::class, 'approver');

    return [
        'name' => $faker->word,
        'description' => $faker->sentence,
        'approved_flag' => true,
        'approved_by' => $approver->id
    ];
});

$factory->state(App\Project::class, 'unpublished', function (Faker $faker) {
    $approver = create_state(User::class, 'approver');

    return [
        'name' => $faker->word,
        'description' => $faker->sentence,
        'published_at' => null,
        'unpublished_at' => null
    ];
});

$factory->state(App\Project::class, 'published', function (Faker $faker) {
    $approver = create_state(User::class, 'approver');

    return [
        'name' => $faker->word,
        'description' => $faker->sentence,
        'published_at' => '2000-01-01 00:00:00',
        'unpublished_at' => null
    ];
});



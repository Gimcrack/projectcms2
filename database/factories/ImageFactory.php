<?php

use Faker\Generator as Faker;
use Illuminate\Http\Testing\File;

$factory->define(App\Image::class, function (Faker $faker) {
    $name = $faker->word;

    $file = File::image('project-image.png', $width = 1920, $height = 1080);

    return [
        'path' => $file->storeAs('images',$name,'public'),
        'subject_id' => null,
        'subject_type' => null
    ];
});
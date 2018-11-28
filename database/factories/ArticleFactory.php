<?php

use Faker\Generator as Faker;

$factory->define(App\Article::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(15, true),
        'body'  => $faker->paragraph(50, true)
    ];
});

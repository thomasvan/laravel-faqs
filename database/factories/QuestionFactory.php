<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Question;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'title' => rtrim($faker->sentence(rand(5, 10)), '.'),
        'body' => $faker->paragraphs(rand(3, 7), true),
        'views' => rand(0, 10),
        'votes_count' => rand(-4, 10)
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Book;
use Faker\Generator as Faker;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(4),
        'isbn' => $faker->unique()->isbn13,
        'country' => $faker->country,
        'number_of_pages' => $faker->numberBetween(250, 2500),
        'publisher' => $faker->company,
        'release_date' => $faker->date('Y-m-d')
    ];
});

<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(config('auth.model'), function ($faker) {
    $name = $faker->name;

    return [
        'name' => $name,
        'email' => $faker->email,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
        'confirmed' => 1,
        'slug' => str_slug($name),
        'is_banned' => 0
    ];
});


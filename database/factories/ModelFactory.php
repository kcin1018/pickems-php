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

$factory->define(Pickems\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => str_random(10),
        'remember_token' => str_random(10),
        'admin' => false,
    ];
});

$factory->define(Pickems\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'paid' => $faker->boolean,
    ];
});

$factory->define(Pickems\NflTeam::class, function (Faker\Generator $faker) {
    return [
        'abbr' => strtoupper(str_random(3)),
        'conference' => $faker->randomElement(['NFC', 'AFC']),
        'city' => $faker->city,
        'name' => $faker->company,
    ];
});

$factory->define(Pickems\NflPlayer::class, function (Faker\Generator $faker) {
    return [
        'gsis_id' => str_random(10),
        'profile_id' => str_random(10),
        'name' => $faker->name,
        'position' => $faker->randomElement(['QB', 'RB', 'FB', 'TE', 'WR', 'K']),
        'active' => $faker->boolean,
    ];
});

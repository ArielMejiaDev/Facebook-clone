<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Friend;
use App\User;
use Faker\Generator as Faker;

$factory->define(Friend::class, function (Faker $faker) {
    return [
        'friend_id' => factory(User::class),
    ];
});

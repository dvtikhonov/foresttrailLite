<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Device::class, function (Faker $faker) {
    return [
        //
        'imei' => $faker->unique()->Uuid ,
        'alias' => $faker->unique()->slug(1),
        'options'=> $faker->text(200),
        'name' => \Faker\Factory::create('ru_RU')->firstName,
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProviderPos::class, function (Faker $faker) {

    return [
        'name' => \Faker\Factory::create('ru_RU')->name,
        'address' => \Faker\Factory::create('ru_RU')->address,
        'contacts' => \Faker\Factory::create('ru_RU')->company,
        'phone' => 8095123127000+random_int(0,999),
        'balance' => random_int(0,999),
        'minimum_balance' => random_int(0,9),
        'is_blocked' => random_int(0,1),
        'lon' =>  87.14899443984100 +random_int(0,999)/100000000000000,
        'lat' =>  53.75644007085900 +random_int(0,999)/100000000000000,
    ];
});

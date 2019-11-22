<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\TrackerSession::class, function (Faker $faker) {

//    dd (__METHOD__, 777);

    $createdAt = $faker->dateTimeBetween('-3 months','-1 months');

    return [
        //
        'lat' => 53.82251116 +  rand(1.0000000000001, 10000)/1000000000000 ,
        'lon' => 87.159061815 +  rand(1.0000000000001, 10000)/1000000000000,
        'user_id'=> rand(1,3),
        'name' => (rand(1,3)==3) ? 'Нет данных' : 'Z',
        'is_checked' => rand(0,1),
        'created_at'=>  $createdAt,
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TransferDevice::class, function (Faker $faker) {
//    $deviceIds = \App\Models\Device::pluck('id')->toArray();
//    $providerPosIds = \App\Models\ProviderPos::pluck('id')->toArray();

    return [
        //
//        'device_id' => array_random($deviceIds) ,
//        'provider_pos_id' => array_random($providerPosIds) ,
        'transferred_at'=> $faker->dateTimeBetween('-10 days', '-1 days'),
    ];
});

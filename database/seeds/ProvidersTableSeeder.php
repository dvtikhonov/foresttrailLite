<?php

use Illuminate\Database\Seeder;

class ProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = \App\User::pluck('id')->toArray();

        $data = [
            [   'name' => 'Роман',
                'address' => \Faker\Factory::create('ru_RU')->address,
                'phone' => 8095123123000+random_int(0,999),
                'contacts' => \Faker\Factory::create('ru_RU')->company,
                'user_id' => array_random($userIds),
//                'created_at'=> date('Y-m-d H:i:s'),
//                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            [   'name' => \Faker\Factory::create('ru_RU')->name,
                'address' => \Faker\Factory::create('ru_RU')->address,
                'phone' => 8095123123000+random_int(0,999),
                'contacts' => \Faker\Factory::create('ru_RU')->company,
                'user_id' => array_random($userIds),
//                'created_at'=> date('Y-m-d H:i:s'),
//                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            [   'name' => \Faker\Factory::create('ru_RU')->name,
                'address' => \Faker\Factory::create('ru_RU')->address,
                'phone' => 8095123123000+random_int(0,999),
                'contacts' => \Faker\Factory::create('ru_RU')->company,
                'user_id' => array_random($userIds),
//                'created_at'=> date('Y-m-d H:i:s'),
//                'updated_at'=> date('Y-m-d H:i:s'),
            ],

        ];

        \Illuminate\Support\Facades\DB::table('providers')->insert($data);
//                dd(__METHOD__, $data );
    }
}

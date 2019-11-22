<?php
//namespace database\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [   'name' => 'Admin',
                'email' => \Faker\Factory::create('ru_RU')->email,
                'phone' => '9133310802',
                'role'  => 'admin',
                'password' =>bcrypt(12345678),
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            [   'name' => \Faker\Factory::create('ru_RU')->name,
                'email' => \Faker\Factory::create('ru_RU')->email,
                'phone' => \Faker\Factory::create('ru_RU')->phoneNumber,
                'role'  => 'client',
                'password' =>bcrypt(12345678),
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            [   'name' => \Faker\Factory::create('ru_RU')->name,
                'email' => \Faker\Factory::create('ru_RU')->email,
                'phone' => \Faker\Factory::create('ru_RU')->phoneNumber,
                'role'  => 'client',
                'password' =>bcrypt(12345678),
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],

        ];

//        dd(__METHOD__, $data);

        DB::table('users')->insert($data);
        //
    }
}
